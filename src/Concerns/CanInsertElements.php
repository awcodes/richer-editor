<?php

declare(strict_types=1);

namespace Awcodes\RicherEditor\Concerns;

use DOMDocument;
use DOMElement;
use DOMText;
use DOMXPath;

trait CanInsertElements
{
    /**
     * Insert random code tags into content, avoiding anchor and code elements.
     */
    public function insertRandomCodeTags(string $content, float $probability = 0.1): string
    {
        return $this->insertRandomElements(
            content: $content,
            probability: $probability,
            elementCreator: function (DOMDocument $dom, string $elementContent): DOMElement {
                return $dom->createElement('code', $elementContent);
            },
            afterElementCallback: function (DOMDocument $dom, \DOMDocumentFragment $fragment, string $trailingWhitespace, string $sentenceEndingPunctuation, array $words, int $j, int $wordCount): void {
                // Add sentence-ending punctuation after the closing tag if it exists
                if ($sentenceEndingPunctuation !== '') {
                    $fragment->appendChild($dom->createTextNode($sentenceEndingPunctuation));
                }

                // Add trailing whitespace after the closing tag if it exists
                if ($trailingWhitespace !== '') {
                    $fragment->appendChild($dom->createTextNode($trailingWhitespace));
                }
            },
        );
    }

    /**
     * Insert random anchor tags into content, avoiding anchor and code elements.
     */
    public function insertRandomAnchorTags(string $content, float $probability = 0.1): string
    {
        return $this->insertRandomElements(
            content: $content,
            probability: $probability,
            elementCreator: function (DOMDocument $dom, string $elementContent): DOMElement {
                $anchorElement = $dom->createElement('a', $elementContent);
                $anchorElement->setAttribute('href', $this->faker->url());

                return $anchorElement;
            },
            afterElementCallback: function (DOMDocument $dom, \DOMDocumentFragment $fragment, string $trailingWhitespace, string $sentenceEndingPunctuation, array $words, int $j, int $wordCount): void {
                // Add sentence-ending punctuation after the closing tag if it exists
                if ($sentenceEndingPunctuation !== '') {
                    $fragment->appendChild($dom->createTextNode($sentenceEndingPunctuation));
                }

                // Check if we need to add space after the closing tag
                if ($trailingWhitespace !== '') {
                    // There was trailing whitespace, preserve it
                    $fragment->appendChild($dom->createTextNode($trailingWhitespace));
                } elseif ($j < $wordCount && $sentenceEndingPunctuation === '') {
                    // No trailing whitespace and no sentence-ending punctuation, check what comes next
                    $nextItem = $words[$j];

                    // If next item is whitespace, we don't need to add space
                    if (mb_trim($nextItem) === '') {
                        // Next is whitespace, don't add space
                    } else {
                        // Next is a word, check if it starts with punctuation
                        $nextChar = mb_substr(mb_trim($nextItem), 0, 1);

                        // Add space if next character is not sentence-ending punctuation
                        if (! in_array($nextChar, ['.', '!', '?', ',', ';', ':', ')', ']', '}'])) {
                            $fragment->appendChild($dom->createTextNode(' '));
                        }
                    }
                }
            },
        );
    }

    /**
     * Generic method to insert random HTML elements into content.
     *
     * @param  callable(DOMDocument, string): DOMElement  $elementCreator
     * @param  callable(DOMDocument, \DOMDocumentFragment, string, string, array, int, int): void  $afterElementCallback
     */
    protected function insertRandomElements(
        string $content,
        float $probability,
        callable $elementCreator,
        callable $afterElementCallback
    ): string {
        if (mb_trim($content) === '') {
            return $content;
        }

        $dom = $this->createDomDocument($content);
        $textNodes = $this->getTextNodesToProcess($dom);

        if ($textNodes === false) {
            return $content;
        }

        $nodesToProcess = $this->collectNodesToProcess($textNodes);

        foreach ($nodesToProcess as $textNode) {
            /** @var DOMText $textNode */
            $text = $textNode->nodeValue;

            // Skip if text is empty or only whitespace
            if (mb_trim($text) === '') {
                continue;
            }

            // Split text into words, preserving whitespace
            $words = preg_split('/(\s+)/u', $text, -1, PREG_SPLIT_DELIM_CAPTURE);

            if ($words === false || count($words) === 0) {
                continue;
            }

            $fragment = $dom->createDocumentFragment();
            $hasModifications = false;
            $wordCount = count($words);
            $i = 0;

            while ($i < $wordCount) {
                $word = $words[$i];

                // Skip whitespace-only entries (they're preserved as-is)
                if (mb_trim($word) === '') {
                    $fragment->appendChild($dom->createTextNode($word));
                    $i++;

                    continue;
                }

                // Randomly decide whether to wrap this word
                if (mt_rand() / mt_getrandmax() < $probability) {
                    $wrappedData = $this->collectWordsToWrap($words, $i, $wordCount);

                    if ($wrappedData['text'] !== '') {
                        $processedData = $this->processWrappedText($wrappedData['text']);

                        // Only create element if we have non-whitespace content
                        if (mb_trim($processedData['content']) !== '') {
                            $element = $elementCreator($dom, $processedData['content']);
                            $fragment->appendChild($element);

                            $afterElementCallback(
                                $dom,
                                $fragment,
                                $processedData['trailingWhitespace'],
                                $processedData['sentenceEndingPunctuation'],
                                $words,
                                $wrappedData['nextIndex'],
                                $wordCount
                            );

                            $hasModifications = true;
                            $i = $wrappedData['nextIndex'];
                        } else {
                            $fragment->appendChild($dom->createTextNode($word));
                            $i++;
                        }
                    } else {
                        $fragment->appendChild($dom->createTextNode($word));
                        $i++;
                    }
                } else {
                    $fragment->appendChild($dom->createTextNode($word));
                    $i++;
                }
            }

            if ($hasModifications) {
                // Replace the text node with the fragment
                $parent = $textNode->parentNode;
                if ($parent !== null) {
                    $parent->replaceChild($fragment, $textNode);
                }
            }
        }

        return $this->extractContentFromDom($dom);
    }

    /**
     * Create and configure a DOMDocument from HTML content.
     */
    protected function createDomDocument(string $content): DOMDocument
    {
        $dom = new DOMDocument('1.0', 'UTF-8');

        // Wrap content in a container to handle fragments
        $wrappedContent = '<div>'.$content.'</div>';

        // Suppress warnings for malformed HTML
        libxml_use_internal_errors(true);
        @$dom->loadHTML($wrappedContent, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        return $dom;
    }

    /**
     * Get text nodes that are not inside anchor or code elements.
     */
    protected function getTextNodesToProcess(DOMDocument $dom): \DOMNodeList|false
    {
        $xpath = new DOMXPath($dom);

        // Get all text nodes that are not inside anchor or code elements
        return $xpath->query('//text()[not(ancestor::a) and not(ancestor::code)]');
    }

    /**
     * Collect text nodes into an array for processing.
     *
     * @return array<int, DOMText>
     */
    protected function collectNodesToProcess(\DOMNodeList $textNodes): array
    {
        $nodesToProcess = [];
        foreach ($textNodes as $textNode) {
            $nodesToProcess[] = $textNode;
        }

        return $nodesToProcess;
    }

    /**
     * Collect consecutive words to wrap (1-5 words).
     *
     * @param  array<int, string>  $words
     * @return array{text: string, nextIndex: int}
     */
    protected function collectWordsToWrap(array $words, int $startIndex, int $wordCount): array
    {
        // Determine how many words to wrap (1-5)
        $wordsToWrap = mt_rand(1, 5);
        $wrappedText = '';
        $wordsWrapped = 0;
        $j = $startIndex;

        // Collect consecutive words and whitespace between them
        while ($j < $wordCount && $wordsWrapped < $wordsToWrap) {
            $currentItem = $words[$j];

            if (mb_trim($currentItem) !== '') {
                // This is a word
                $wrappedText .= $currentItem;
                $wordsWrapped++;
                $j++;

                // Include following whitespace if it exists
                if ($j < $wordCount && mb_trim($words[$j]) === '') {
                    $wrappedText .= $words[$j];
                    $j++;
                }
            } else {
                // This is whitespace - include it if we've wrapped at least one word
                if ($wordsWrapped > 0) {
                    $wrappedText .= $currentItem;
                    $j++;
                } else {
                    break;
                }
            }
        }

        return [
            'text' => $wrappedText,
            'nextIndex' => $j,
        ];
    }

    /**
     * Process wrapped text to extract trailing whitespace and sentence-ending punctuation.
     *
     * @return array{content: string, trailingWhitespace: string, sentenceEndingPunctuation: string}
     */
    protected function processWrappedText(string $wrappedText): array
    {
        // Extract trailing whitespace and trim it from the wrapped text
        $trailingWhitespace = '';
        $trimmedText = mb_rtrim($wrappedText, " \t\n\r\0\x0B");

        // Check if there was trailing whitespace
        if (mb_strlen($trimmedText) < mb_strlen($wrappedText)) {
            $trailingWhitespace = mb_substr($wrappedText, mb_strlen($trimmedText));
        }

        // Extract sentence-ending punctuation from the end of the text
        $sentenceEndingPunctuation = '';
        $content = $trimmedText;

        if (mb_strlen($trimmedText) > 0) {
            $lastChar = mb_substr($trimmedText, -1);
            if (in_array($lastChar, ['.', '!', '?'])) {
                $sentenceEndingPunctuation = $lastChar;
                $content = mb_substr($trimmedText, 0, -1);
            }
        }

        return [
            'content' => $content,
            'trailingWhitespace' => $trailingWhitespace,
            'sentenceEndingPunctuation' => $sentenceEndingPunctuation,
        ];
    }

    /**
     * Extract content from the DOM wrapper div.
     */
    protected function extractContentFromDom(DOMDocument $dom): string
    {
        // Extract the content from the wrapper div
        $body = $dom->getElementsByTagName('div')->item(0);

        if ($body === null) {
            return '';
        }

        $result = '';
        foreach ($body->childNodes as $node) {
            $result .= $dom->saveHTML($node);
        }

        return $result;
    }
}

