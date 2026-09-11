<?php

namespace App\Filament\Resources\HomePageResource\Pages;

use App\Filament\Concerns\HandlesLegacyRemoteImages;
use App\Filament\Concerns\NormalizesFileUploadState;
use App\Filament\Resources\HomePageResource;
use Filament\Resources\Pages\EditRecord;

class EditHomePage extends EditRecord
{
    use HandlesLegacyRemoteImages;
    use NormalizesFileUploadState;

    protected static string $resource = HomePageResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data = $this->nullifyLegacyRemoteImageFields($data, [
            'hero_image',
            'experience_image',
            'resorts_card_image',
            'guesthouses_card_image',
            'city_hotels_card_image',
            'liveaboards_card_image',
        ]);

        $data['arabic_content'] = array_replace_recursive(
            \App\Models\HomePage::arabicContentDefaults(),
            $data['arabic_content'] ?? [],
        );
        foreach (['kicker', 'heading_line_one', 'heading_line_two', 'heading_emphasis', 'description'] as $field) {
            $arabicField = 'arabic_'.$field;
            $data[$arabicField] ??= data_get(\App\Models\HomePage::arabicContentDefaults(), 'hero.'.$field);
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $this->restoreLegacyRemoteImageFields($data, $this->getRecord(), [
            'hero_image',
            'experience_image',
            'resorts_card_image',
            'guesthouses_card_image',
            'city_hotels_card_image',
            'liveaboards_card_image',
        ]);
    }

    protected function legacyUploadStatePaths(): array
    {
        return [
            'data.hero_image',
            'data.experience_image',
            'data.resorts_card_image',
            'data.guesthouses_card_image',
            'data.city_hotels_card_image',
            'data.liveaboards_card_image',
        ];
    }
}
