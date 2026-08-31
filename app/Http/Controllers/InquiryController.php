<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Inquiry;
use App\Services\ReCaptchaVerifier;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InquiryController extends Controller
{
    public function store(Request $request, ReCaptchaVerifier $reCaptchaVerifier): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'nullable|string|max:100|required_without:first_name',
            'first_name' => 'nullable|string|max:100|required_without:name',
            'last_name' => 'nullable|string|max:100',
            'phone' => 'required|string|max:30',
            'email' => 'nullable|email|max:150',
            'nationality' => 'nullable|string|max:100',
            'travel_type' => 'nullable|string|max:30',
            'accommodation_id' => 'nullable|integer|exists:accommodations,id',
            'room_id' => 'nullable|integer|exists:rooms,id',
            'arrival_date' => 'nullable|date',
            'departure_date' => 'nullable|date|after_or_equal:arrival_date',
            'travellers' => 'nullable|integer|min:1|max:50',
            'adults' => 'nullable|integer|min:1|max:50',
            'children' => 'nullable|integer|min:0|max:20',
            'children_ages' => 'nullable|string|max:500',
            'infants' => 'nullable|integer|min:0|max:20',
            'budget' => 'nullable|string|max:100',
            'preferred_atoll' => 'nullable|string|max:100',
            'transfer_preference' => 'nullable|string|max:100',
            'honeymoon' => 'nullable|boolean',
            'family_trip' => 'nullable|boolean',
            'diving_trip' => 'nullable|boolean',
            'surfing_trip' => 'nullable|boolean',
            'preferred_room' => 'nullable|string|max:255',
            'meal_plan' => 'nullable|string|max:255',
            'message' => 'nullable|string|max:3000',
            'source' => 'nullable|string|max:100',
            'utm_source' => 'nullable|string|max:100',
            'utm_medium' => 'nullable|string|max:100',
            'utm_campaign' => 'nullable|string|max:100',
            'utm_content' => 'nullable|string|max:150',
            'landing_page' => 'nullable|string|max:2048',
            'website' => 'nullable|string|size:0',
            'recaptcha_token' => $reCaptchaVerifier->enabled() ? 'required|string' : 'nullable|string',
            'recaptcha_action' => 'nullable|string|max:100',
        ]);

        $recaptchaAction = $data['recaptcha_action'] ?? 'inquiry_submit';

        if (! $reCaptchaVerifier->verify($data['recaptcha_token'] ?? '', $recaptchaAction, $request->ip())) {
            throw ValidationException::withMessages([
                'form' => 'We could not verify this submission. Please try again or message us on WhatsApp.',
            ]);
        }

        unset($data['website'], $data['recaptcha_token'], $data['recaptcha_action']);

        if (blank($data['name'] ?? null)) {
            $data['name'] = trim(collect([$data['first_name'] ?? null, $data['last_name'] ?? null])->filter()->implode(' '));
        }

        $attribution = array_filter(
            $request->session()->get('marketing_attribution', []),
            fn ($value) => filled($value)
        );

        $inquiry = DB::transaction(function () use ($data, $attribution, $request) {
            [$firstName, $lastName] = $this->splitName($data['name']);

            $customer = Customer::query()
                ->when($data['email'] ?? null, fn ($query, $email) => $query->orWhere('email', $email))
                ->when($data['phone'] ?? null, fn ($query, $phone) => $query->orWhere('phone', $phone)->orWhere('whatsapp', $phone))
                ->first();

            if ($customer) {
                $customer->fill([
                    'first_name' => $customer->first_name ?: $firstName,
                    'last_name' => $customer->last_name ?: $lastName,
                    'email' => $data['email'] ?? $customer->email,
                    'phone' => $data['phone'] ?? $customer->phone,
                    'whatsapp' => $data['phone'] ?? $customer->whatsapp,
                    'country' => $data['nationality'] ?? $customer->country,
                ])->save();
            } else {
                $customer = Customer::create([
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $data['email'] ?? null,
                    'phone' => $data['phone'] ?? null,
                    'whatsapp' => $data['phone'] ?? null,
                    'country' => $data['nationality'] ?? null,
                ]);
            }

            $checkIn = $data['arrival_date'] ?? null;
            $checkOut = $data['departure_date'] ?? null;
            $nights = ($checkIn && $checkOut)
                ? max(0, (int) Carbon::parse($checkIn)->diffInDays(Carbon::parse($checkOut)))
                : null;

            return Inquiry::create($data + [
                'customer_id' => $customer->id,
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                'number_of_nights' => $nights,
                'adults' => $data['adults'] ?? $data['travellers'] ?? 2,
                'children' => $data['children'] ?? 0,
                'infants' => $data['infants'] ?? 0,
                'source' => $data['source'] ?? 'website',
                'utm_source' => $data['utm_source'] ?? ($attribution['utm_source'] ?? null),
                'utm_medium' => $data['utm_medium'] ?? ($attribution['utm_medium'] ?? null),
                'utm_campaign' => $data['utm_campaign'] ?? ($attribution['utm_campaign'] ?? null),
                'utm_content' => $data['utm_content'] ?? ($attribution['utm_content'] ?? null),
                'landing_page' => $data['landing_page'] ?? ($attribution['landing_page'] ?? $request->fullUrlWithoutQuery(['utm_source', 'utm_medium', 'utm_campaign', 'utm_content'])),
                'status' => 'new',
            ]);
        });

        return back()->with('success', 'Thank you. We have received your inquiry. Reference: '.$inquiry->reference.'.');
    }

    private function splitName(string $name): array
    {
        $parts = preg_split('/\s+/', trim($name), 2) ?: [];

        return [
            $parts[0] ?? $name,
            $parts[1] ?? '',
        ];
    }
}
