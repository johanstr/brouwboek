<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Open = 'open';
    case Packed = 'packed';
    case Invoiced = 'invoiced';

    // De tekst die de brouwer op het scherm ziet
    public function label(): string
    {
        return match ($this) {
            self::Open => 'Open',
            self::Packed => 'Ingepakt',
            self::Invoiced => 'Gefactureerd',
        };
    }

    // De kleur van het label (een Flux-badge)
    public function color(): string
    {
        return match ($this) {
            self::Open => 'amber',
            self::Packed => 'sky',
            self::Invoiced => 'green',
        };
    }
}
