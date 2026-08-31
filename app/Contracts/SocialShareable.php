<?php

namespace App\Contracts;

interface SocialShareable
{
    public function socialShareType(): string;

    public function socialShareTitleFallback(): string;

    public function socialShareDescriptionFallback(): string;

    public function socialShareCanonicalUrl(): string;

    public function socialSharePrimaryImageUrl(): string;

    public function socialShareLocationLabel(): ?string;

    public function socialShareCategoryLabel(): ?string;

    public function socialShareSlugValue(): string;
}
