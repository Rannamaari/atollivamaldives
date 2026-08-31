<?php

namespace App\Services;

use App\Contracts\SocialShareable;
use App\Enums\SocialSharePlatform;
use App\Models\SiteSetting;
use App\Support\Social\SocialShareData;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class SocialShareService
{
    public function __construct(
        protected SiteSetting $siteSetting,
    ) {
    }

    public function for(SocialShareable $model): SocialShareData
    {
        $settings = $this->siteSetting->current();
        $canonicalUrl = $model->socialShareCanonicalUrl();
        $hashtags = $this->hashtagsFor($model);
        $title = $this->titleFor($model);
        $description = $this->descriptionFor($model);
        $image = $this->imageFor($model);
        $caption = $this->captionFor($model, $canonicalUrl, $description, $hashtags);
        $utmContent = $this->utmContentFor($model);

        return new SocialShareData(
            title: $title,
            description: $description,
            image: $image,
            url: $canonicalUrl,
            canonicalUrl: $canonicalUrl,
            caption: $caption,
            hashtags: $hashtags,
            platformType: $model->socialShareType(),
            facebookUrl: 'https://www.facebook.com/sharer/sharer.php?u='.rawurlencode($this->trackedUrl($model, SocialSharePlatform::Facebook)),
            xUrl: 'https://twitter.com/intent/tweet?text='.rawurlencode($this->xTextFor($model, $description)).'&url='.rawurlencode($this->trackedUrl($model, SocialSharePlatform::X)).'&hashtags='.rawurlencode(implode(',', array_map(fn (string $tag): string => ltrim($tag, '#'), array_slice($hashtags, 0, 4)))),
            whatsappUrl: 'https://wa.me/?text='.rawurlencode($this->whatsAppTextFor($model, $description)),
            copyLinkUrl: $this->trackedUrl($model, SocialSharePlatform::CopyLink),
            nativeShareUrl: $this->trackedUrl($model, SocialSharePlatform::Native),
            siteName: $settings->site_name ?: 'Atolliva Maldives',
            twitterCard: 'summary_large_image',
            ogType: $model->socialShareType() === 'post' ? 'article' : 'website',
            contentType: $model->socialShareType(),
            contentId: (string) $model->getKey(),
            contentLabel: $title,
            utmContent: $utmContent,
        );
    }

    public function trackedUrl(SocialShareable $model, SocialSharePlatform $platform): string
    {
        $canonical = $model->socialShareCanonicalUrl();
        $parts = parse_url($canonical) ?: [];
        $query = [];

        if (! empty($parts['query'])) {
            parse_str($parts['query'], $query);
        }

        $query = array_merge($query, [
            'utm_source' => $platform->value,
            'utm_medium' => 'social',
            'utm_campaign' => 'share',
            'utm_content' => $this->utmContentFor($model),
        ]);

        $base = Arr::get($parts, 'scheme') && Arr::get($parts, 'host')
            ? Arr::get($parts, 'scheme').'://'.Arr::get($parts, 'host').(Arr::get($parts, 'port') ? ':'.Arr::get($parts, 'port') : '').Arr::get($parts, 'path', '')
            : $canonical;

        return $base.'?'.http_build_query($query);
    }

    protected function titleFor(SocialShareable $model): string
    {
        return trim((string) ($model->social_title ?: $model->seo_title ?: $model->socialShareTitleFallback()));
    }

    protected function descriptionFor(SocialShareable $model): string
    {
        return Str::limit(
            trim((string) ($model->social_description ?: $model->seo_description ?: $model->socialShareDescriptionFallback())),
            200,
            ''
        );
    }

    protected function imageFor(SocialShareable $model): string
    {
        foreach (['social_image', 'generated_social_image'] as $field) {
            $value = $model->{$field} ?? null;

            if (filled($value)) {
                return str_starts_with((string) $value, 'http')
                    ? (string) $value
                    : asset('storage/'.ltrim((string) $value, '/'));
            }
        }

        return $model->socialSharePrimaryImageUrl();
    }

    protected function captionFor(SocialShareable $model, string $canonicalUrl, string $description, array $hashtags): string
    {
        if (filled($model->social_caption ?? null)) {
            return trim((string) $model->social_caption);
        }

        $intro = match ($model->socialShareType()) {
            'post' => 'Planning a Maldives holiday?',
            'package' => 'Your Maldives escape starts here.',
            default => 'Explore this Maldives stay with Atolliva Maldives.',
        };

        return trim(implode("\n\n", array_filter([
            '🌴 '.$this->titleFor($model),
            $intro,
            Str::finish($description, '.'),
            'View details:'."\n".$canonicalUrl,
            implode(' ', $hashtags),
        ])));
    }

    protected function hashtagsFor(SocialShareable $model): array
    {
        $manual = $this->normalizeHashtags((string) ($model->social_hashtags ?? ''));

        if ($manual !== []) {
            return $manual;
        }

        $defaults = match ($model->socialShareType()) {
            'resort' => ['Maldives', 'MaldivesResorts', $model->socialShareSlugValue(), 'MaldivesHoliday'],
            'guesthouse' => ['Maldives', $model->socialShareLocationLabel() ?: 'LocalIsland', 'MaldivesGuesthouse', 'VisitMaldives'],
            'liveaboard' => ['Maldives', 'LiveaboardMaldives', 'MaldivesDiving', 'MaldivesHoliday'],
            'package' => ['Maldives', 'MaldivesPackages', 'MaldivesHoliday', 'VisitMaldives'],
            'post' => ['MaldivesTravel', 'VisitMaldives', 'MaldivesGuide'],
            default => ['Maldives', 'AtollivaMaldives', 'VisitMaldives'],
        };

        $siteDefaults = $this->normalizeHashtags((string) ($this->siteSetting->current()->default_share_hashtags ?? ''));

        return array_slice($this->deduplicateHashtags([...$defaults, ...$siteDefaults]), 0, 5);
    }

    protected function normalizeHashtags(string $value): array
    {
        return $this->deduplicateHashtags(
            collect(preg_split('/[\s,]+/', trim($value)) ?: [])
                ->filter()
                ->map(function (string $tag): string {
                    $normalized = preg_replace('/[^A-Za-z0-9]+/', '', Str::studly(ltrim($tag, '#'))) ?: '';

                    return $normalized === '' ? '' : '#'.$normalized;
                })
                ->filter()
                ->all()
        );
    }

    protected function deduplicateHashtags(array $hashtags): array
    {
        return array_values(array_unique(array_filter(array_map(function (string $tag): string {
            $normalized = preg_replace('/[^A-Za-z0-9]+/', '', Str::studly(ltrim($tag, '#'))) ?: '';

            return $normalized === '' ? '' : '#'.Str::limit($normalized, 28, '');
        }, $hashtags))));
    }

    protected function xTextFor(SocialShareable $model, string $description): string
    {
        return Str::limit(
            'Looking at '.$this->titleFor($model).'? '.Str::finish($description, '.'),
            220,
            '...'
        );
    }

    protected function whatsAppTextFor(SocialShareable $model, string $description): string
    {
        return trim(implode("\n\n", [
            '🌴 '.$this->titleFor($model),
            Str::finish($description, '.'),
            'View details:'."\n".$this->trackedUrl($model, SocialSharePlatform::WhatsApp),
            'Need a personalised quotation? Atolliva Maldives can help.',
        ]));
    }

    protected function utmContentFor(SocialShareable $model): string
    {
        return Str::slug($model->socialShareSlugValue());
    }
}
