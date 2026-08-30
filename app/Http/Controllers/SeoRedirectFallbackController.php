<?php

namespace App\Http\Controllers;

use App\Models\SeoRedirect;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SeoRedirectFallbackController extends Controller
{
    public function __invoke(): RedirectResponse
    {
        $redirect = SeoRedirect::findActiveForPath(request()->path());

        if (! $redirect) {
            throw new NotFoundHttpException();
        }

        $redirect->increment('hits');

        return redirect($redirect->destination_path, $redirect->http_status);
    }
}
