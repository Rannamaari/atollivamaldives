<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class RequestQuoteController extends Controller
{
    public function __invoke(): View
    {
        return view('inquiries.request-quote');
    }
}
