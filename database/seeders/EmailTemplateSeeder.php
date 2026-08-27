<?php

namespace Database\Seeders;

use App\Enums\EmailTemplateType;
use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class EmailTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'name' => 'Supplier Introduction',
                'template_type' => EmailTemplateType::SupplierIntroduction,
                'subject_template' => 'Greetings from Atolliva Maldives',
                'body_template' => "Dear {{contact_person}},\n\nGreetings from Atolliva Maldives.\n\nMy name is {{sender_name}}, and I am writing on behalf of Atolliva Maldives, a Maldives-based travel agency.\n\nWe are currently expanding our portfolio of Maldives travel products and would be pleased to explore a direct working relationship with {{property_name}}.\n\n{{personalized_intro}}\n\n{{partnership_request}}\n\nIf there is a contracting procedure, supplier registration process, or documentation required from our side, kindly let us know.\n\nWarm regards,\n\n{{sender_name}}\n{{sender_designation}}\nAtolliva Maldives",
                'description' => 'Use for a first supplier introduction before requesting rates or contracting details.',
            ],
            [
                'name' => 'Supplier / Resort Rate Request',
                'template_type' => EmailTemplateType::RequestB2BRates,
                'subject_template' => 'Request for Travel Trade Rates & Contracting - Atolliva Maldives',
                'body_template' => "Dear {{contact_person}},\n\nGreetings from Atolliva Maldives.\n\nMy name is {{sender_name}}, and I am writing on behalf of Atolliva Maldives, a Maldives-based travel agency.\n\nWe are currently expanding our portfolio of resorts, hotels, guesthouses, liveaboards, and other accommodation partners in the Maldives and would be pleased to establish a direct working relationship with {{property_name}}.\n\nWe kindly request your latest travel trade / B2B rates and contracting information for the upcoming seasons.\n\nPlease share, where applicable:\n\n* Travel agency / B2B room rates\n* Validity period of the rates\n* Room categories\n* Meal plan supplements\n* Extra adult and child rates\n* Child policies\n* Transfer rates and arrangements\n* Green Tax and applicable taxes\n* Minimum stay requirements\n* Special offers and promotions\n* Honeymoon benefits\n* Early bird offers\n* Free night offers\n* Cancellation and no-show policies\n* Payment terms\n* Stop-sale procedures\n* Blackout dates\n* Market restrictions, if any\n* Booking and reservation contact details\n* Sales and contracting contact details\n\nIf you have a rate sheet, contract, fact sheet, resort presentation, images, or promotional materials, we would appreciate it if you could share them with us as well.\n\nRequested rate period:\n\n{{rate_period}}\n\nMarkets:\n\n{{markets}}\n\nAdditional request:\n\n{{specific_request}}\n\nIf you require any company documents from Atolliva Maldives before sharing your contracted rates, please let us know and we will provide the necessary information.\n\nWe look forward to working with {{property_name}} and promoting your property to our clients and international travel agency partners.\n\nWarm regards,\n\n{{sender_name}}\n{{sender_designation}}\nAtolliva Maldives\n\nM. Ithaamuiyge 1\nAlima Magu\nMale\nRepublic of Maldives\n\nTel: +960 9996210 / +960 7779493\nEmail: hello@atollivamaldives.com\nWebsite: atollivamaldives.com",
                'description' => 'Use when requesting B2B rates, contracting details, and supporting documents from Maldives suppliers.',
            ],
            [
                'name' => 'Supplier Agreement Follow-up',
                'template_type' => EmailTemplateType::AgreementFollowUp,
                'subject_template' => 'Follow-up on agreement for {{property_name}}',
                'body_template' => "Dear {{contact_person}},\n\nGreetings from Atolliva Maldives.\n\nA quick follow-up regarding the agreement and next contracting steps for {{property_name}}.\n\nKind regards,\n{{sender_name}}",
                'description' => 'Use for following up on agreements and contracting progress.',
            ],
            [
                'name' => 'International Travel Agency Partnership Introduction',
                'template_type' => EmailTemplateType::AgencyIntroduction,
                'subject_template' => 'Partnership Opportunity with Atolliva Maldives',
                'body_template' => "Dear {{contact_person}},\n\nGreetings from Atolliva Maldives.\n\nMy name is {{sender_name}}, and I am writing on behalf of Atolliva Maldives, a Maldives-based travel agency specializing in holidays, resort stays, guesthouses, liveaboards, transfers, and customized travel arrangements across the Maldives.\n\nWe are currently expanding our network of international travel agency partners and would be pleased to explore a potential B2B partnership with {{company_name}}.\n\nAs your Maldives ground partner, Atolliva Maldives can assist with:\n\n* Resort and hotel accommodation\n* Local island guesthouses\n* Liveaboards\n* Airport and inter-island transfers\n* Honeymoon and holiday packages\n* Customized itineraries\n* Group and FIT arrangements\n* Excursions and other Maldives travel services\n\nWe would be interested in learning more about your agency and discussing opportunities to work together for clients travelling to the Maldives.\n\n{{personalized_intro}}\n\n{{partnership_request}}\n\nIf you have a supplier registration process, contracting procedure, or documentation that we need to complete, kindly share the requirements with us.\n\nWe would also be happy to provide our company profile, travel agency licence/details, destination information, quotations, and other supporting documents upon request.\n\nWe look forward to establishing a long-term and mutually beneficial relationship with {{company_name}}.\n\nWarm regards,\n\n{{sender_name}}\n{{sender_designation}}\nAtolliva Maldives\n\nM. Ithaamuiyge 1\nAlima Magu\nMale\nRepublic of Maldives\n\nTel: +960 9996210 / +960 7779493\nEmail: hello@atollivamaldives.com\nWebsite: atollivamaldives.com",
                'description' => 'Use for introducing Atolliva Maldives to overseas travel agencies and B2B partners.',
            ],
        ];

        foreach ($templates as $template) {
            EmailTemplate::updateOrCreate(
                ['slug' => Str::slug($template['name'])],
                $template + ['is_active' => true],
            );
        }
    }
}
