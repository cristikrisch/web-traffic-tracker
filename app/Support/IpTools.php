<?php

namespace App\Support;

class IpTools {

    public static function truncate(?string $ip): ?string {
        if (!$ip) return null;

        // Zero the last part
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $parts = explode('.', $ip);
            $parts[3] = '0';
            return implode('.', $parts);
        }
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            return preg_replace('/(:[0-9a-f]{1,4}){5}$/i', '::', $ip);
        }
        return null;
    }

    public static function hash(?string $ip, ?string $pepper): ?string {
        return ($ip && $pepper) ? hash_hmac('sha256', $ip, $pepper) : null;
    }
}
