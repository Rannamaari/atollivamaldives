<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use Illuminate\Contracts\View\View;

class QuotationPrintController extends Controller
{
    public function __invoke(Quotation $quotation): View
    {
        abort_unless(auth()->check(), 403);

        return view('quotations.print', [
            'quotation' => $quotation->load(['inquiry', 'customer', 'accommodation']),
            'company' => config('operations_hub.company'),
        ]);
    }
}
