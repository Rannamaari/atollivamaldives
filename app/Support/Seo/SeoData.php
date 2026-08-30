<?php

namespace App\Support\Seo;

use Illuminate\Contracts\Support\Arrayable;

class SeoData implements Arrayable
{
    public function __construct(
        public readonly string $siteName,
        public readonly string $title,
        public readonly string $description,
        public readonly string $canonical,
        public readonly string $ogTitle,
        public readonly string $ogDescription,
        public readonly string $ogImage,
        public readonly string $twitterCard,
        public readonly string $robots,
        public readonly ?string $searchConsoleVerification,
        public readonly array $breadcrumbs = [],
        public readonly array $schema = [],
    ) {
    }

    public function toArray(): array
    {
        return [
            'site_name' => $this->siteName,
            'title' => $this->title,
            'description' => $this->description,
            'canonical' => $this->canonical,
            'og_title' => $this->ogTitle,
            'og_description' => $this->ogDescription,
            'og_image' => $this->ogImage,
            'twitter_card' => $this->twitterCard,
            'robots' => $this->robots,
            'search_console_verification' => $this->searchConsoleVerification,
            'breadcrumbs' => $this->breadcrumbs,
            'schema' => $this->schema,
        ];
    }
}
