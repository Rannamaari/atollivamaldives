<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartnerApplication extends Model
{
    public const STATUSES = [
        'new' => 'New',
        'contacted' => 'Contacted',
        'verified' => 'Verified',
        'approved' => 'Approved',
        'declined' => 'Declined',
    ];

    public const BUSINESS_TYPES = [
        'travel_agency' => 'Travel Agency',
        'tour_operator' => 'Tour Operator',
        'travel_advisor' => 'Travel Advisor',
        'dmc' => 'DMC',
        'ota' => 'OTA',
        'specialist_travel_company' => 'Specialist Travel Company',
        'other' => 'Other',
    ];

    public const ENQUIRY_VOLUMES = [
        'occasional' => 'Occasional',
        '1_5' => '1-5 per month',
        '5_20' => '5-20 per month',
        '20_plus' => '20+ per month',
    ];

    protected $fillable = [
        'status', 'company', 'contact_name', 'country', 'email', 'phone', 'website',
        'business_type', 'markets', 'estimated_enquiries', 'message',
        'marketing_opt_in', 'submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'marketing_opt_in' => 'boolean',
            'submitted_at' => 'datetime',
        ];
    }
}
