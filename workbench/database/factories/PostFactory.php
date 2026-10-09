<?php

declare(strict_types=1);

namespace Workbench\Database\Factories;

use Awcodes\RicherEditor\Support\RichContentFaker;
use Illuminate\Database\Eloquent\Factories\Factory;
use Workbench\App\Models\Post;

/** @extends Factory<Post> */
class PostFactory extends Factory
{
    /** @var class-string<Post> */
    protected $model = Post::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'title' => 'Developing with Richer Editor',
            'content' => RichContentFaker::make()
                ->heading(level: 2)
                ->paragraphs(count: 2, links: true, bold: true, italic: true)
                ->codeBlock(language: 'php')
                ->blockquote()
                ->asJson(),
        ];
    }
}
