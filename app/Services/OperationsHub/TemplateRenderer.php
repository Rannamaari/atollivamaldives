<?php

namespace App\Services\OperationsHub;

use App\Models\AgencyPartner;
use App\Models\EmailTemplate;
use App\Models\RateRequest;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Support\Arr;

class TemplateRenderer
{
    public function placeholderCatalog(): array
    {
        return [
            '{{company_name}}' => ['label' => 'Company name', 'source' => 'Supplier or agency record', 'field' => 'trading_name / legal_name', 'tone' => 'sky'],
            '{{contact_person}}' => ['label' => 'Contact person', 'source' => 'Selected contact record', 'field' => 'full_name', 'tone' => 'amber'],
            '{{contact_name}}' => ['label' => 'Contact name', 'source' => 'Selected contact record', 'field' => 'full_name', 'tone' => 'amber'],
            '{{country}}' => ['label' => 'Country', 'source' => 'Supplier or agency record', 'field' => 'country', 'tone' => 'sky'],
            '{{property_name}}' => ['label' => 'Property name', 'source' => 'Supplier record', 'field' => 'trading_name / legal_name', 'tone' => 'emerald'],
            '{{supplier_name}}' => ['label' => 'Supplier name', 'source' => 'Supplier record', 'field' => 'trading_name / legal_name', 'tone' => 'emerald'],
            '{{supplier_type}}' => ['label' => 'Supplier type', 'source' => 'Supplier record', 'field' => 'supplier_type', 'tone' => 'emerald'],
            '{{agency_name}}' => ['label' => 'Agency name', 'source' => 'Agency record', 'field' => 'trading_name / legal_company_name', 'tone' => 'violet'],
            '{{rate_period}}' => ['label' => 'Rate period', 'source' => 'Rate request record', 'field' => 'requested_rate_period', 'tone' => 'rose'],
            '{{markets}}' => ['label' => 'Markets', 'source' => 'Rate request or Generate Email form', 'field' => 'requested_markets', 'tone' => 'rose'],
            '{{specific_request}}' => ['label' => 'Specific request', 'source' => 'Rate request or Generate Email form', 'field' => 'requested_services', 'tone' => 'rose'],
            '{{personalized_intro}}' => ['label' => 'Personalized intro', 'source' => 'Generate Email form', 'field' => 'manual text', 'tone' => 'teal'],
            '{{partnership_request}}' => ['label' => 'Partnership request', 'source' => 'Generate Email form', 'field' => 'manual text', 'tone' => 'teal'],
            '{{sender_name}}' => ['label' => 'Sender name', 'source' => 'Logged-in admin user', 'field' => 'users.name', 'tone' => 'slate'],
            '{{sender_designation}}' => ['label' => 'Sender designation', 'source' => 'Operations config or Generate Email context', 'field' => 'operations_hub.company.sender_designation', 'tone' => 'slate'],
        ];
    }

    public function render(EmailTemplate $template, array $context = []): array
    {
        $variables = $this->variables($context);

        $subject = strtr($template->subject_template, $variables);
        $body = strtr($template->body_template, $variables);

        return [
            'subject' => $subject,
            'body' => $body,
            'unresolved' => $this->findUnresolvedVariables($subject.' '.$body),
        ];
    }

    public function variables(array $context = []): array
    {
        $supplier = Arr::get($context, 'supplier');
        $agency = Arr::get($context, 'agency');
        $rateRequest = Arr::get($context, 'rate_request');
        $sender = Arr::get($context, 'sender', auth()->user());
        $companyName = $agency instanceof AgencyPartner
            ? ($agency->trading_name ?: $agency->legal_company_name)
            : ($supplier instanceof Supplier ? ($supplier->trading_name ?: $supplier->legal_name) : null);
        $contactName = Arr::get($context, 'contact_name');

        return collect([
            '{{supplier_name}}' => $supplier instanceof Supplier ? ($supplier->trading_name ?: $supplier->legal_name) : null,
            '{{property_name}}' => $supplier instanceof Supplier ? ($supplier->trading_name ?: $supplier->legal_name) : null,
            '{{company_name}}' => $companyName,
            '{{contact_name}}' => $contactName,
            '{{contact_person}}' => $contactName,
            '{{agency_name}}' => $agency instanceof AgencyPartner ? ($agency->trading_name ?: $agency->legal_company_name) : null,
            '{{rate_period}}' => $rateRequest instanceof RateRequest ? $rateRequest->requested_rate_period : null,
            '{{markets}}' => Arr::get($context, 'markets') ?: ($rateRequest instanceof RateRequest ? $rateRequest->requested_markets : null),
            '{{specific_request}}' => Arr::get($context, 'specific_request') ?: ($rateRequest instanceof RateRequest ? $rateRequest->requested_services : null),
            '{{personalized_intro}}' => Arr::get($context, 'personalized_intro'),
            '{{partnership_request}}' => Arr::get($context, 'partnership_request'),
            '{{country}}' => $agency instanceof AgencyPartner ? $agency->country : ($supplier instanceof Supplier ? $supplier->country : null),
            '{{supplier_type}}' => $supplier instanceof Supplier ? $supplier->supplier_type?->label() : null,
            '{{sender_name}}' => $sender instanceof User ? $sender->name : null,
            '{{sender_designation}}' => Arr::get($context, 'sender_designation', config('operations_hub.company.sender_designation')),
            '{{atolliva_name}}' => config('operations_hub.company.name'),
            '{{atolliva_email}}' => config('operations_hub.company.email'),
            '{{atolliva_website}}' => config('operations_hub.company.website'),
            '{{atolliva_license_number}}' => config('operations_hub.company.license_number'),
            '{{next_follow_up_date}}' => Arr::get($context, 'next_follow_up_date'),
        ])->filter(fn ($value) => filled($value))
            ->map(fn ($value) => e((string) $value))
            ->all();
    }

    public function findUnresolvedVariables(string $value): array
    {
        preg_match_all('/\{\{[^}]+\}\}/', $value, $matches);

        return array_values(array_unique($matches[0] ?? []));
    }

    public function describePlaceholders(array $placeholders): array
    {
        $catalog = $this->placeholderCatalog();

        return collect($placeholders)
            ->map(function (string $placeholder) use ($catalog): array {
                return [
                    'placeholder' => $placeholder,
                    'label' => $catalog[$placeholder]['label'] ?? $placeholder,
                    'source' => $catalog[$placeholder]['source'] ?? 'Custom template value',
                    'field' => $catalog[$placeholder]['field'] ?? 'manual entry',
                    'tone' => $catalog[$placeholder]['tone'] ?? 'gray',
                ];
            })
            ->values()
            ->all();
    }
}
