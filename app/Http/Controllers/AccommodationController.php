<?php

namespace App\Http\Controllers;

use App\Enums\AccommodationType;
use App\Models\Accommodation;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccommodationController extends Controller
{
    public function index(Request $request): View
    {
        $type = $request->route('type') ?: $request->string('type')->toString();
        $selectedType = collect(AccommodationType::cases())->first(fn (AccommodationType $case) => $case->value === $type);
        $destination = trim($request->string('destination')->toString());
        $checkIn = $request->string('check_in')->toString();
        $checkOut = $request->string('check_out')->toString();
        $adults = max(1, (int) $request->integer('adults', 2));
        $children = max(0, (int) $request->integer('children', 0));

        $items = Accommodation::published()
            ->with(['facilities', 'transfers', 'atollRelation', 'islandRelation'])
            ->withCount(['rooms as published_rooms_count' => fn ($query) => $query->where('status', 'published')])
            ->when($selectedType, fn ($query) => $query->where('type', $selectedType->value))
            ->when($destination, function ($query, $destination) {
                $query->where(function ($nested) use ($destination) {
                    $nested
                        ->where('name', 'like', '%'.$destination.'%')
                        ->orWhere('island', 'like', '%'.$destination.'%')
                        ->orWhere('atoll', 'like', '%'.$destination.'%')
                        ->orWhere('city', 'like', '%'.$destination.'%')
                        ->orWhereHas('islandRelation', fn ($island) => $island->where('name', 'like', '%'.$destination.'%'))
                        ->orWhereHas('atollRelation', fn ($atoll) => $atoll->where('name', 'like', '%'.$destination.'%'));
                });
            })
            ->orderBy('sort_order')
            ->paginate(12)
            ->withQueryString();

        return view('accommodations.index', [
            'items' => $items,
            'selectedType' => $selectedType,
            'searchState' => [
                'destination' => $destination,
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                'adults' => $adults,
                'children' => $children,
            ],
        ]);
    }

    public function show(Accommodation $accommodation): View
    {
        abort_unless($accommodation->published, 404);

        $accommodation->load([
            'facilities',
            'transfers',
            'galleryImages',
            'atollRelation',
            'islandRelation',
            'rooms' => fn ($query) => $query
                ->where('status', 'published')
                ->with(['facilities', 'images'])
                ->orderBy('sort_order'),
        ]);

        $similarProperties = Accommodation::published()
            ->with(['facilities'])
            ->whereKeyNot($accommodation->getKey())
            ->where(function ($query) use ($accommodation) {
                $query
                    ->where('type', $accommodation->type->value)
                    ->orWhere('atoll', $accommodation->atoll);
            })
            ->orderByDesc('featured')
            ->orderBy('sort_order')
            ->take(3)
            ->get();

        return view('accommodations.show', [
            'accommodation' => $accommodation,
            'similarProperties' => $similarProperties,
        ]);
    }
}
