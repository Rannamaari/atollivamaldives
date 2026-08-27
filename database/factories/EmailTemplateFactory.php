<?php

namespace Database\Factories;

use App\Enums\EmailTemplateType;
use App\Models\EmailTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class EmailTemplateFactory extends Factory
{
    protected $model = EmailTemplate::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);

        return [
            'name' => Str::title($name),
            'slug' => Str::slug($name),
            'template_type' => EmailTemplateType::SupplierIntroduction,
            'subject_template' => 'Hello {{supplier_name}}',
            'body_template' => 'Dear {{contact_name}}, from {{atolliva_name}}',
            'is_active' => true,
        ];
    }
}
