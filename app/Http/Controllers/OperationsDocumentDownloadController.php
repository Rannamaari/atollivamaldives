<?php

namespace App\Http\Controllers;

use App\Models\DocumentRecord;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class OperationsDocumentDownloadController extends Controller
{
    public function __invoke(DocumentRecord $document): BinaryFileResponse
    {
        abort_unless(auth()->check(), 403);
        abort_unless(Storage::disk('local')->exists($document->stored_path), 404);

        return response()->download(
            Storage::disk('local')->path($document->stored_path),
            $document->original_filename
        );
    }
}
