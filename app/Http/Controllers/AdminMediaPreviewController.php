<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminMediaPreviewController extends Controller
{
    public function __invoke(string $path): StreamedResponse
    {
        $path = ltrim($path, '/');

        abort_if(str_contains($path, '..'), 404);
        abort_unless(in_array(strtolower(pathinfo($path, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp'], true), 404);

        $disk = Storage::disk('public');

        abort_unless($disk->exists($path), 404);

        return $disk->response($path, basename($path), [
            'Cache-Control' => 'private, max-age=300',
            'Content-Disposition' => 'inline; filename="'.basename($path).'"',
        ]);
    }
}
