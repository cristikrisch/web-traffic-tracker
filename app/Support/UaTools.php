<?php
namespace App\Support;

class UaTools
{
    public static function hash(?string $ua): ?string
    {
        return $ua ? hash('sha256', $ua) : null;
    }
}
