<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        $galleries = [
            'ocean-explorer' => [
                'https://images.unsplash.com/photo-1544551763-46a013bb70d5?auto=format&fit=crop&w=1600&q=85',
                'https://images.unsplash.com/photo-1500375592092-40eb2168fd21?auto=format&fit=crop&w=1600&q=85',
                'https://images.unsplash.com/photo-1567899378494-47b22a2ae96a?auto=format&fit=crop&w=1600&q=85',
            ],
            'ocean-pearl' => [
                'https://images.unsplash.com/photo-1567899378494-47b22a2ae96a?auto=format&fit=crop&w=1600&q=85',
                'https://images.unsplash.com/photo-1562281302-809108fd533c?auto=format&fit=crop&w=1600&q=85',
                'https://images.unsplash.com/photo-1569263979104-865ab7cd8d13?auto=format&fit=crop&w=1600&q=85',
            ],
            'blue-horizon-voyager' => [
                'https://images.unsplash.com/photo-1562281302-809108fd533c?auto=format&fit=crop&w=1600&q=85',
                'https://images.unsplash.com/photo-1544551763-77ef2d0cfc6c?auto=format&fit=crop&w=1600&q=85',
                'https://images.unsplash.com/photo-1500375592092-40eb2168fd21?auto=format&fit=crop&w=1600&q=85',
            ],
            'coral-wind-charter' => [
                'https://images.unsplash.com/photo-1500375592092-40eb2168fd21?auto=format&fit=crop&w=1600&q=85',
                'https://images.unsplash.com/photo-1569263979104-865ab7cd8d13?auto=format&fit=crop&w=1600&q=85',
                'https://images.unsplash.com/photo-1562281302-809108fd533c?auto=format&fit=crop&w=1600&q=85',
            ],
            'manta-quest' => [
                'https://images.unsplash.com/photo-1544551763-77ef2d0cfc6c?auto=format&fit=crop&w=1600&q=85',
                'https://images.unsplash.com/photo-1567899378494-47b22a2ae96a?auto=format&fit=crop&w=1600&q=85',
                'https://images.unsplash.com/photo-1500375592092-40eb2168fd21?auto=format&fit=crop&w=1600&q=85',
            ],
            'sea-story-maldives' => [
                'https://images.unsplash.com/photo-1569263979104-865ab7cd8d13?auto=format&fit=crop&w=1600&q=85',
                'https://images.unsplash.com/photo-1567899378494-47b22a2ae96a?auto=format&fit=crop&w=1600&q=85',
                'https://images.unsplash.com/photo-1544551763-77ef2d0cfc6c?auto=format&fit=crop&w=1600&q=85',
            ],
        ];

        foreach ($galleries as $slug => $images) {
            DB::table('accommodations')
                ->where('slug', $slug)
                ->update([
                    'images' => json_encode($images),
                    'updated_at' => now(),
                ]);
        }
    }

    public function down(): void
    {
        $coverImages = [
            'ocean-explorer' => 'https://images.unsplash.com/photo-1544551763-46a013bb70d5?auto=format&fit=crop&w=1400&q=85',
            'ocean-pearl' => 'https://images.unsplash.com/photo-1567899378494-47b22a2ae96a?auto=format&fit=crop&w=1600&q=85',
            'blue-horizon-voyager' => 'https://images.unsplash.com/photo-1562281302-809108fd533c?auto=format&fit=crop&w=1600&q=85',
            'coral-wind-charter' => 'https://images.unsplash.com/photo-1500375592092-40eb2168fd21?auto=format&fit=crop&w=1600&q=85',
            'manta-quest' => 'https://images.unsplash.com/photo-1544551763-77ef2d0cfc6c?auto=format&fit=crop&w=1600&q=85',
            'sea-story-maldives' => 'https://images.unsplash.com/photo-1569263979104-865ab7cd8d13?auto=format&fit=crop&w=1600&q=85',
        ];

        foreach ($coverImages as $slug => $image) {
            DB::table('accommodations')
                ->where('slug', $slug)
                ->update([
                    'images' => json_encode([$image]),
                    'updated_at' => now(),
                ]);
        }
    }
};
