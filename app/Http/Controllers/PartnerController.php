<?php

namespace App\Http\Controllers;

use App\Models\HomePage;
use App\Models\PartnerApplication;
use App\Services\ReCaptchaVerifier;
use App\Support\Seo\SeoManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PartnerController extends Controller
{
    public function index(SeoManager $seoManager): View
    {
        $homePage = HomePage::active()->latest('updated_at')->first() ?? new HomePage;
        $title = 'Maldives B2B Travel Partner & DMC | Atolliva Maldives';
        $description = 'Partner with Atolliva Maldives for Maldives resort rates, transfers, experiences and tailor-made holidays. B2B support for travel agencies, tour operators and travel advisors worldwide.';

        return view('partners.index', [
            'homePage' => $homePage,
            'businessTypes' => PartnerApplication::BUSINESS_TYPES,
            'enquiryVolumes' => PartnerApplication::ENQUIRY_VOLUMES,
            'seo' => $seoManager->forSimplePage(
                title: $title,
                description: $description,
                canonical: route('partners.index'),
                breadcrumbs: [
                    ['name' => 'Home', 'url' => route('home')],
                    ['name' => 'Partner With Us', 'url' => route('partners.index')],
                ],
                image: $homePage->hero_image_url,
                extraSchema: [[
                    '@context' => 'https://schema.org',
                    '@type' => 'Service',
                    '@id' => route('partners.index').'#maldives-dmc-service',
                    'name' => 'Maldives B2B Travel Partner and Ground Handling',
                    'serviceType' => 'Maldives destination management and B2B travel support',
                    'provider' => ['@id' => url('/').'#travel-agency'],
                    'areaServed' => ['@type' => 'Country', 'name' => 'Maldives'],
                    'audience' => [
                        '@type' => 'BusinessAudience',
                        'audienceType' => 'Travel agencies, tour operators, travel advisors and DMCs',
                    ],
                    'url' => route('partners.index'),
                ]],
            )->toArray(),
        ]);
    }

    public function store(Request $request, ReCaptchaVerifier $recaptchaVerifier): RedirectResponse
    {
        $rules = [
            'company' => ['required', 'string', 'max:180'],
            'contact_name' => ['required', 'string', 'max:150'],
            'country' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email:rfc', 'max:180'],
            'phone' => ['required', 'string', 'max:60'],
            'company_website' => ['nullable', 'url', 'max:255'],
            'business_type' => ['nullable', Rule::in(array_keys(PartnerApplication::BUSINESS_TYPES))],
            'markets' => ['nullable', 'string', 'max:1000'],
            'estimated_enquiries' => ['nullable', Rule::in(array_keys(PartnerApplication::ENQUIRY_VOLUMES))],
            'message' => ['required', 'string', 'max:3000'],
            'marketing_opt_in' => ['nullable', 'boolean'],
            'company_fax' => ['nullable', 'string', 'size:0'],
        ];

        if ($recaptchaVerifier->enabled()) {
            $rules['recaptcha_token'] = ['required', 'string'];
            $rules['recaptcha_action'] = ['required', 'in:partner_application_submit'];
        }

        $data = $request->validate($rules);

        if (! $recaptchaVerifier->verify(
            token: (string) ($data['recaptcha_token'] ?? ''),
            action: 'partner_application_submit',
            ip: $request->ip(),
        )) {
            throw ValidationException::withMessages([
                'form' => 'We could not verify this submission. Please refresh the page and try again.',
            ]);
        }

        PartnerApplication::create([
            'status' => 'new',
            'company' => $data['company'],
            'contact_name' => $data['contact_name'],
            'country' => $data['country'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'website' => $data['company_website'] ?? null,
            'business_type' => $data['business_type'] ?? null,
            'markets' => $data['markets'] ?? null,
            'estimated_enquiries' => $data['estimated_enquiries'] ?? null,
            'message' => $data['message'] ?? null,
            'marketing_opt_in' => $request->boolean('marketing_opt_in'),
            'submitted_at' => now(),
        ]);

        return redirect(route('partners.index').'#partner-form')->with(
            'partner_success',
            'Thank you. Our Maldives team has received your enquiry and will contact you shortly.'
        );
    }
}
