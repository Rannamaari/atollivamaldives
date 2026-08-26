<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class AboutController extends Controller
{
    public function __invoke(): View
    {
        return view('about', [
            'officeAddress' => 'M. Ithaamuiyge 1, Aliasmagu, Maldives',
        ]);
    }
}
