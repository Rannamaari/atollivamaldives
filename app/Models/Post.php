<?php

namespace App\Models;

use App\Contracts\SocialShareable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model implements SocialShareable
{
    use HasFactory;

    protected $fillable = ['title', 'slug', 'category', 'blog_offer_id', 'excerpt', 'body', 'arabic_title', 'arabic_excerpt', 'arabic_body', 'arabic_seo_title', 'arabic_seo_description', 'featured_image', 'author', 'published', 'featured', 'published_at', 'seo_title', 'seo_description', 'social_title', 'social_description', 'social_caption', 'social_hashtags', 'social_image', 'generated_social_image'];

    protected function casts(): array
    {
        return ['published' => 'boolean', 'featured' => 'boolean', 'published_at' => 'datetime'];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('published', true)->where(fn ($q) => $q->whereNull('published_at')->orWhere('published_at', '<=', now()));
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function blogOffer()
    {
        return $this->belongsTo(BlogOffer::class);
    }

    public function publicPathForSlug(?string $slug = null): string
    {
        return route('blog.show', ['post' => $slug ?? $this->slug], false);
    }

    public function arabicPublicPath(): string
    {
        return route('blog.arabic.show', ['post' => $this->slug], false);
    }

    public function hasArabicTranslation(): bool
    {
        return filled($this->arabic_title) && filled($this->arabic_body);
    }

    public function seoTitleFallback(): string
    {
        return $this->title.' | Atolliva Maldives';
    }

    public function seoDescriptionFallback(): string
    {
        return (string) ($this->seo_description ?: $this->excerpt ?: str($this->body)->stripTags()->squish()->limit(160));
    }

    public function arabicSeoTitleFallback(): string
    {
        return trim((string) ($this->arabic_seo_title ?: $this->arabic_title.' | Atolliva Maldives'));
    }

    public function arabicSeoDescriptionFallback(): string
    {
        return (string) ($this->arabic_seo_description ?: $this->arabic_excerpt ?: str($this->arabic_body)->stripTags()->squish()->limit(160));
    }

    public function seoImageUrl(): string
    {
        if (filled($this->featured_image)) {
            return str_starts_with((string) $this->featured_image, 'http')
                ? (string) $this->featured_image
                : asset('storage/'.ltrim((string) $this->featured_image, '/'));
        }

        return asset('logo/optimized/atolliva-share.png');
    }

    public function seoBreadcrumbs(): array
    {
        return [
            ['name' => 'Home', 'url' => route('home')],
            ['name' => 'Blog', 'url' => route('blog.index')],
            ['name' => $this->title, 'url' => url($this->publicPathForSlug())],
        ];
    }

    public function arabicSeoBreadcrumbs(): array
    {
        return [
            ['name' => 'الرئيسية', 'url' => route('home')],
            ['name' => 'المدونة', 'url' => route('blog.index')],
            ['name' => $this->arabic_title, 'url' => url($this->arabicPublicPath())],
        ];
    }

    public function socialShareType(): string
    {
        return 'post';
    }

    public function socialShareTitleFallback(): string
    {
        return $this->seoTitleFallback();
    }

    public function socialShareDescriptionFallback(): string
    {
        return $this->seoDescriptionFallback();
    }

    public function socialShareCanonicalUrl(): string
    {
        return url($this->publicPathForSlug());
    }

    public function socialSharePrimaryImageUrl(): string
    {
        return $this->seoImageUrl();
    }

    public function socialShareLocationLabel(): ?string
    {
        return null;
    }

    public function socialShareCategoryLabel(): ?string
    {
        return $this->category;
    }

    public function socialShareSlugValue(): string
    {
        return $this->slug ?: $this->title;
    }
}
