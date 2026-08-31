<?php

namespace App\Enums;

enum SocialSharePlatform: string
{
    case Native = 'native';
    case WhatsApp = 'whatsapp';
    case Facebook = 'facebook';
    case X = 'x';
    case CopyLink = 'copy_link';
    case CopyCaption = 'copy_caption';

    public function label(): string
    {
        return match ($this) {
            self::Native => 'More',
            self::WhatsApp => 'WhatsApp',
            self::Facebook => 'Facebook',
            self::X => 'X',
            self::CopyLink => 'Copy Link',
            self::CopyCaption => 'Copy Caption',
        };
    }
}
