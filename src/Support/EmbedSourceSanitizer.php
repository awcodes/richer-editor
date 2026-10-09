<?php

declare(strict_types=1);

namespace Awcodes\RicherEditor\Support;

use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;
use Symfony\Component\HtmlSanitizer\Visitor\AttributeSanitizer\AttributeSanitizerInterface;

/**
 * Keeps an iframe's src only when it is an https URL on one of the given hosts. The sanitizer's media host list
 * would restrict images too, so iframes are checked on their own.
 */
class EmbedSourceSanitizer implements AttributeSanitizerInterface
{
    /**
     * @param  array<string>  $hosts
     */
    public function __construct(
        protected array $hosts,
    ) {}

    /**
     * @return array<string>
     */
    public function getSupportedElements(): array
    {
        return ['iframe'];
    }

    /**
     * @return array<string>
     */
    public function getSupportedAttributes(): array
    {
        return ['src'];
    }

    public function sanitizeAttribute(string $element, string $attribute, string $value, HtmlSanitizerConfig $config): ?string
    {
        $url = parse_url($value);

        if (($url['scheme'] ?? null) !== 'https') {
            return null;
        }

        if (! in_array(strtolower($url['host'] ?? ''), $this->hosts, true)) {
            return null;
        }

        return $value;
    }
}
