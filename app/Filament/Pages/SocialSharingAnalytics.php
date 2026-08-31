<?php

namespace App\Filament\Pages;

use App\Models\Accommodation;
use App\Models\Post;
use App\Models\SocialShareEvent;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Throwable;

class SocialSharingAnalytics extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-share';

    protected static ?string $navigationGroup = 'SEO';

    protected static ?string $navigationLabel = 'Social Sharing';

    protected static string $view = 'filament.pages.social-sharing-analytics';

    public function getViewData(): array
    {
        $range = request()->string('range')->toString() ?: '30';

        if (! Schema::hasTable('social_share_events')) {
            return $this->emptyViewData($range);
        }

        try {
            $from = match ($range) {
                '7' => now()->subDays(7),
                '30' => now()->subDays(30),
                '90' => now()->subDays(90),
                default => null,
            };

            $events = SocialShareEvent::query()
                ->when($from, fn ($query) => $query->where('created_at', '>=', $from))
                ->get();

            $platformTotals = collect(['whatsapp', 'facebook', 'x', 'native', 'copy_link', 'copy_caption'])
                ->mapWithKeys(fn (string $platform): array => [$platform => $events->where('platform', $platform)->count()]);

            $grouped = $events
                ->groupBy(fn (SocialShareEvent $event): string => $event->shareable_type.'#'.$event->shareable_id)
                ->map(function (Collection $items, string $key): array {
                    [$type, $id] = explode('#', $key);
                    $record = match ($type) {
                        Post::class => Post::query()->find($id),
                        Accommodation::class => Accommodation::query()->find($id),
                        default => null,
                    };

                    return [
                        'label' => $record?->title ?? $record?->name ?? 'Unknown content',
                        'type' => $record instanceof Post ? 'Blog Post' : ($record?->type?->label() ?? 'Content'),
                        'whatsapp' => $items->where('platform', 'whatsapp')->count(),
                        'facebook' => $items->where('platform', 'facebook')->count(),
                        'x' => $items->where('platform', 'x')->count(),
                        'native' => $items->where('platform', 'native')->count(),
                        'copy_link' => $items->where('platform', 'copy_link')->count(),
                        'copy_caption' => $items->where('platform', 'copy_caption')->count(),
                        'total' => $items->count(),
                    ];
                })
                ->sortByDesc('total')
                ->values();

            return [
                'range' => $range,
                'platformTotals' => $platformTotals,
                'topContent' => $grouped->take(10),
                'generatedAt' => Carbon::now(),
                'errorMessage' => null,
            ];
        } catch (Throwable $exception) {
            Log::error('Unable to render social sharing analytics page.', [
                'message' => $exception->getMessage(),
                'class' => $exception::class,
            ]);

            return $this->emptyViewData(
                $range,
                'Analytics data is temporarily unavailable. The issue has been logged so the admin area stays usable.'
            );
        }
    }

    protected function emptyViewData(string $range, ?string $errorMessage = null): array
    {
        return [
            'range' => $range,
            'platformTotals' => collect(['whatsapp', 'facebook', 'x', 'native', 'copy_link', 'copy_caption'])
                ->mapWithKeys(fn (string $platform): array => [$platform => 0]),
            'topContent' => collect(),
            'generatedAt' => Carbon::now(),
            'errorMessage' => $errorMessage,
        ];
    }
}
