<?php

namespace App\Http\Controllers;

use App\Enums\SocialSharePlatform;
use App\Models\Accommodation;
use App\Models\Post;
use App\Models\SiteSetting;
use App\Models\SocialShareEvent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SocialShareTrackingController extends Controller
{
    public function __invoke(Request $request, SiteSetting $siteSetting): JsonResponse
    {
        $settings = $siteSetting->current();

        if (! $settings->enable_share_tracking) {
            return response()->json(['tracked' => false]);
        }

        $data = $request->validate([
            'content_type' => ['required', Rule::in(['post', 'resort', 'guesthouse', 'liveaboard', 'package', 'city_hotel'])],
            'content_id' => ['required', 'integer'],
            'platform' => ['required', Rule::in(array_map(fn (SocialSharePlatform $platform): string => $platform->value, SocialSharePlatform::cases()))],
            'url' => ['nullable', 'url', 'max:2048'],
        ]);

        $model = match ($data['content_type']) {
            'post' => Post::query()->find($data['content_id']),
            default => Accommodation::query()
                ->whereKey($data['content_id'])
                ->where('type', $data['content_type'])
                ->first(),
        };

        abort_unless($model, 422, 'Invalid share target.');

        SocialShareEvent::create([
            'shareable_type' => $model::class,
            'shareable_id' => $model->getKey(),
            'platform' => $data['platform'],
            'url' => $data['url'] ?? null,
            'session_id' => $request->session()->getId(),
            'user_id' => Auth::id(),
            'ip_hash' => $request->ip() ? hash('sha256', $request->ip()) : null,
            'user_agent' => $request->userAgent(),
            'referrer' => $request->headers->get('referer'),
        ]);

        return response()->json(['tracked' => true]);
    }
}
