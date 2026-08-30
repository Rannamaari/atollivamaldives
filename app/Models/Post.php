<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'slug', 'category', 'blog_offer_id', 'excerpt', 'body', 'featured_image', 'author', 'published', 'featured', 'published_at', 'seo_title', 'seo_description'];

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

    public function seoTitleFallback(): string
    {
        return $this->title.' | Atolliva Maldives';
    }

    public function seoDescriptionFallback(): string
    {
        return (string) ($this->seo_description ?: $this->excerpt ?: str($this->body)->stripTags()->squish()->limit(160));
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
}
