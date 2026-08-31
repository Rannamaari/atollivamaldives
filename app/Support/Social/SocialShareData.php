<?php

namespace App\Support\Social;

use Illuminate\Contracts\Support\Arrayable;

class SocialShareData implements Arrayable
{
    public function __construct(
        public readonly string $title,
        public readonly string $description,
        public readonly string $image,
        public readonly string $url,
        public readonly string $canonicalUrl,
        public readonly string $caption,
        public readonly array $hashtags,
        public readonly string $platformType,
        public readonly string $facebookUrl,
        public readonly string $xUrl,
        public readonly string $whatsappUrl,
        public readonly string $copyLinkUrl,
        public readonly string $nativeShareUrl,
        public readonly string $siteName = 'Atolliva Maldives',
        public readonly string $twitterCard = 'summary_large_image',
        public readonly string $ogType = 'website',
        public readonly string $contentType = '',
        public readonly string|int $contentId = '',
        public readonly string $contentLabel = '',
        public readonly string $utmContent = '',
    ) {
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'image' => $this->image,
            'url' => $this->url,
            'canonical_url' => $this->canonicalUrl,
            'caption' => $this->caption,
            'hashtags' => $this->hashtags,
            'platform_type' => $this->platformType,
            'facebook_url' => $this->facebookUrl,
            'x_url' => $this->xUrl,
            'whatsapp_url' => $this->whatsappUrl,
            'copy_link_url' => $this->copyLinkUrl,
            'native_share_url' => $this->nativeShareUrl,
            'site_name' => $this->siteName,
            'twitter_card' => $this->twitterCard,
            'og_type' => $this->ogType,
            'content_type' => $this->contentType,
            'content_id' => $this->contentId,
            'content_label' => $this->contentLabel,
            'utm_content' => $this->utmContent,
        ];
    }
}
