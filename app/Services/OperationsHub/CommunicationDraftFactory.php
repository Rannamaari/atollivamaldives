<?php

namespace App\Services\OperationsHub;

use App\Enums\CommunicationChannel;
use App\Enums\CommunicationDirection;
use App\Enums\CommunicationStatus;
use App\Enums\EmailTemplateType;
use App\Models\AgencyContact;
use App\Models\AgencyPartner;
use App\Models\Communication;
use App\Models\EmailTemplate;
use App\Models\RateRequest;
use App\Models\Supplier;
use App\Models\SupplierContact;
use App\Models\User;

class CommunicationDraftFactory
{
    public function __construct(
        private readonly TemplateRenderer $templateRenderer,
        private readonly FollowUpScheduler $followUpScheduler,
    ) {}

    public function createDraft(
        ?Supplier $supplier = null,
        ?AgencyPartner $agencyPartner = null,
        ?RateRequest $rateRequest = null,
        SupplierContact|AgencyContact|null $contact = null,
        ?EmailTemplate $template = null,
        ?EmailTemplateType $templateType = null,
        array $templateContext = [],
        array $overrides = [],
    ): Communication {
        $template ??= $this->resolveTemplate($templateType);
        $templateContext = $this->withDefaultTemplateContext(
            supplier: $supplier ?? $rateRequest?->supplier,
            agencyPartner: $agencyPartner,
            rateRequest: $rateRequest,
            contact: $contact,
            templateType: $templateType,
            templateContext: $templateContext,
        );

        $rendered = $template
            ? $this->templateRenderer->render($template, [
                'supplier' => $supplier ?? $rateRequest?->supplier,
                'agency' => $agencyPartner,
                'contact_name' => $templateContext['contact_name'] ?? $contact?->full_name,
                'rate_request' => $rateRequest,
                ...$templateContext,
            ])
            : ['subject' => '', 'body' => '', 'unresolved' => []];

        $content = $this->buildDraftContent(
            supplier: $supplier ?? $rateRequest?->supplier,
            agencyPartner: $agencyPartner,
            rateRequest: $rateRequest,
            contact: $contact,
            templateType: $templateType,
            rendered: $rendered,
        );

        $defaultFollowUp = $this->defaultFollowUpAttributes($templateType, $rateRequest, $agencyPartner);

        return Communication::create([
            'supplier_id' => $supplier?->id ?? $rateRequest?->supplier_id,
            'supplier_contact_id' => $contact instanceof SupplierContact ? $contact->id : null,
            'agency_partner_id' => $agencyPartner?->id,
            'agency_contact_id' => $contact instanceof AgencyContact ? $contact->id : null,
            'rate_request_id' => $rateRequest?->id,
            'direction' => CommunicationDirection::Outbound,
            'channel' => CommunicationChannel::EmailDraft,
            'status' => CommunicationStatus::Draft,
            'recipient' => $contact?->email ?: $agencyPartner?->email,
            'subject' => $content['subject'],
            'body' => $content['body'],
            'drafted_at' => now(),
            'logged_by' => auth()->id(),
            ...$defaultFollowUp,
            ...$overrides,
        ]);
    }

    protected function resolveTemplate(?EmailTemplateType $templateType): ?EmailTemplate
    {
        if (! $templateType) {
            return null;
        }

        return EmailTemplate::query()
            ->where('template_type', $templateType->value)
            ->where('is_active', true)
            ->first();
    }

    protected function defaultFollowUpAttributes(
        ?EmailTemplateType $templateType,
        ?RateRequest $rateRequest = null,
        ?AgencyPartner $agencyPartner = null,
    ): array {
        if ($rateRequest || in_array($templateType, [
            EmailTemplateType::SupplierIntroduction,
            EmailTemplateType::RequestB2BRates,
            EmailTemplateType::FirstSupplierFollowUp,
            EmailTemplateType::SecondSupplierFollowUp,
            EmailTemplateType::RequestUpdatedRates,
            EmailTemplateType::RequestSupplierAgreement,
            EmailTemplateType::AgreementFollowUp,
        ], true)) {
            return [
                'follow_up_required' => true,
                'next_follow_up_at' => $this->followUpScheduler
                    ->nextBusinessDate((int) config('operations_hub.follow_up.supplier_rate_request_business_days'))
                    ->setTime(10, 0),
            ];
        }

        if ($agencyPartner || in_array($templateType, [
            EmailTemplateType::AgencyIntroduction,
            EmailTemplateType::AgencyPartnershipInvitation,
            EmailTemplateType::FirstAgencyFollowUp,
            EmailTemplateType::SecondAgencyFollowUp,
            EmailTemplateType::RequestAgencyDocuments,
        ], true)) {
            return [
                'follow_up_required' => true,
                'next_follow_up_at' => $this->followUpScheduler
                    ->nextBusinessDate((int) config('operations_hub.follow_up.agency_intro_business_days'))
                    ->setTime(10, 0),
            ];
        }

        return [
            'follow_up_required' => false,
            'next_follow_up_at' => null,
        ];
    }

    protected function withDefaultTemplateContext(
        ?Supplier $supplier = null,
        ?AgencyPartner $agencyPartner = null,
        ?RateRequest $rateRequest = null,
        SupplierContact|AgencyContact|null $contact = null,
        ?EmailTemplateType $templateType = null,
        array $templateContext = [],
    ): array {
        $propertyName = $supplier?->trading_name ?: $supplier?->legal_name ?: 'your property';
        $companyName = $agencyPartner?->trading_name ?: $agencyPartner?->legal_company_name ?: $propertyName;
        $contactName = $contact?->full_name
            ?: ($agencyPartner ? 'Partnership Team' : 'Reservations Team');

        $defaults = [
            'contact_name' => $contactName,
            'personalized_intro' => $agencyPartner
                ? "We believe {$companyName} could be a strong fit for our Maldives-focused clients and trade relationships."
                : "We came across {$propertyName} and would be pleased to explore how we can introduce your product to our clients and B2B network.",
            'partnership_request' => $agencyPartner
                ? 'If you are open to collaboration, we would be glad to arrange a short introduction call and share our company profile.'
                : 'If you are open to a direct partnership, we would be happy to receive your contracting process, trade terms, and the best contact details for future coordination.',
            'specific_request' => 'Please include any rate sheets, fact sheets, transfer details, offers, and contracting notes you would like us to review.',
        ];

        if ($rateRequest) {
            $defaults['specific_request'] = $rateRequest->requested_services
                ?: 'Please include room categories, meal plans, transfer rates, child policy, offers, and any supporting trade documents.';
        }

        if ($templateType === EmailTemplateType::SupplierIntroduction) {
            $defaults['partnership_request'] = 'If you are open to a direct partnership, kindly share the best contracting or sales contact and any supplier onboarding requirements.';
        }

        return array_merge($defaults, array_filter($templateContext, fn ($value) => filled($value)));
    }

    protected function buildDraftContent(
        ?Supplier $supplier = null,
        ?AgencyPartner $agencyPartner = null,
        ?RateRequest $rateRequest = null,
        SupplierContact|AgencyContact|null $contact = null,
        ?EmailTemplateType $templateType = null,
        array $rendered = [],
    ): array {
        $identifier = $this->draftIdentifier($supplier, $agencyPartner, $rateRequest);
        $subject = trim((string) ($rendered['subject'] ?? ''));
        $body = trim((string) ($rendered['body'] ?? ''));

        if ($subject === '' || $body === '') {
            return $this->fallbackDraftContent($identifier, $supplier, $agencyPartner, $rateRequest, $contact, $templateType);
        }

        $plainSubject = str($subject)->replaceStart('['.$identifier.'] ', '')->toString();

        $subject = '['.$identifier.'] Greetings from Atolliva Maldives'
            .($plainSubject !== '' ? ' | '.$plainSubject : '');

        if (! str($body)->contains('Greetings from Atolliva Maldives')) {
            $body = "Greetings from Atolliva Maldives.\n\n".$body;
        }

        return [
            'subject' => $subject,
            'body' => $body,
        ];
    }

    protected function fallbackDraftContent(
        string $identifier,
        ?Supplier $supplier = null,
        ?AgencyPartner $agencyPartner = null,
        ?RateRequest $rateRequest = null,
        SupplierContact|AgencyContact|null $contact = null,
        ?EmailTemplateType $templateType = null,
    ): array {
        $sender = auth()->user();
        $senderName = $sender instanceof User ? $sender->name : 'Atolliva Maldives';
        $contactName = $contact?->full_name ?: 'Team';
        $propertyName = $supplier?->trading_name ?: $supplier?->legal_name;
        $agencyName = $agencyPartner?->trading_name ?: $agencyPartner?->legal_company_name;
        $ratePeriod = $rateRequest?->requested_rate_period ?: 'your current contracting period';

        if ($agencyPartner || $templateType === EmailTemplateType::AgencyIntroduction) {
            return [
                'subject' => '['.$identifier.'] Greetings from Atolliva Maldives',
                'body' => "Greetings from Atolliva Maldives.\n\nHello {$contactName},\n\nWe would love to introduce Atolliva Maldives and explore a potential partnership with {$agencyName}.\n\nPlease let us know the best contact point and next step to discuss working together.\n\nKind regards,\n{$senderName}",
            ];
        }

        if ($rateRequest || in_array($templateType, [
            EmailTemplateType::RequestB2BRates,
            EmailTemplateType::FirstSupplierFollowUp,
            EmailTemplateType::SecondSupplierFollowUp,
            EmailTemplateType::RequestUpdatedRates,
            EmailTemplateType::RequestSupplierAgreement,
            EmailTemplateType::AgreementFollowUp,
        ], true)) {
            return [
                'subject' => '['.$identifier.'] Greetings from Atolliva Maldives | Rates Request',
                'body' => "Greetings from Atolliva Maldives.\n\nHello {$contactName},\n\nCould you please share your rates and current B2B terms for {$propertyName} for {$ratePeriod}?\n\nWe would also appreciate any applicable offers, meal plans, transfer details, child policy, and commission structure.\n\nKind regards,\n{$senderName}",
            ];
        }

        return [
            'subject' => '['.$identifier.'] Greetings from Atolliva Maldives',
            'body' => "Greetings from Atolliva Maldives.\n\nHello {$contactName},\n\nWe would love to introduce Atolliva Maldives and explore a partnership opportunity for {$propertyName}.\n\nPlease let us know the best contact point and next step for working together regarding {$propertyName}.\n\nKind regards,\n{$senderName}",
        ];
    }

    protected function draftIdentifier(
        ?Supplier $supplier = null,
        ?AgencyPartner $agencyPartner = null,
        ?RateRequest $rateRequest = null,
    ): string {
        if ($rateRequest) {
            return 'ATL-RATE-'.str_pad((string) $rateRequest->getKey(), 4, '0', STR_PAD_LEFT);
        }

        if ($agencyPartner) {
            return 'ATL-AGY-'.str_pad((string) $agencyPartner->getKey(), 4, '0', STR_PAD_LEFT);
        }

        if ($supplier) {
            return 'ATL-SUP-'.str_pad((string) $supplier->getKey(), 4, '0', STR_PAD_LEFT);
        }

        return 'ATL-DRAFT-'.now()->format('YmdHis');
    }
}
