<?php
namespace App\Support;

class UrlTools
{
    public static function canonical(string $url): string
    {
        $parts = parse_url($url);
        if (!$parts || empty($parts['host']))
            return $url;

        $scheme = strtolower($parts['scheme'] ?? 'https');
        $host   = strtolower($parts['host']);
        $path   = $parts['path'] ?? '/';

        // Strip trailing slash (except root)
        if ($path !== '/' && str_ends_with($path, '/')) {
            $path = rtrim($path, '/');
        }

        return "{$scheme}://{$host}{$path}";
    }
}
