<?php
namespace App\Support;

class UrlTools
{
    public static function canonical(string $url): string
    {
        $parts = parse_url($url);
        if (!$parts || empty($parts['host'])) return $url;

        $scheme = strtolower($parts['scheme'] ?? 'https');
        $host   = strtolower($parts['host']);
        $path   = $parts['path'] ?? '/';

        // strip trailing slash (except root)
        if ($path !== '/' && str_ends_with($path, '/')) {
            $path = rtrim($path, '/');
        }

        return "{$scheme}://{$host}{$path}";
    }

    public static function extractUtm(?string $url): array
    {
        $qs = parse_url($url, PHP_URL_QUERY);
        parse_str($qs ?? '', $params);
        return [
            'utm_source'   => $params['utm_source']   ?? null,
            'utm_medium'   => $params['utm_medium']   ?? null,
            'utm_campaign' => $params['utm_campaign'] ?? null,
            'utm_term'     => $params['utm_term']     ?? null,
            'utm_content'  => $params['utm_content']  ?? null,
        ];
    }
}
