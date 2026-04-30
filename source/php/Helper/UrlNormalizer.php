<?php

namespace ModularityAOLinks\Helper;

/**
 * Normalizes URLs for stable duplicate detection.
 */
class UrlNormalizer
{
    /**
     * Normalize a URL for comparison (scheme/host/path/query, lowercase host, trimmed path).
     *
     * @param string $url Raw URL.
     * @return string Normalized string; empty if input unusable.
     */
    public static function normalize(string $url): string
    {
        $url = trim($url);

        if ($url === '') {
            return '';
        }

        $parts = wp_parse_url($url);

        if (! is_array($parts)) {
            return '';
        }

        $scheme = isset($parts['scheme']) ? strtolower((string) $parts['scheme']) : 'https';
        $host = isset($parts['host']) ? strtolower((string) $parts['host']) : '';

        if ($host === '' && ! empty($parts['path']) && str_starts_with((string) $parts['path'], '/')) {
            return rtrim($url, '/');
        }

        $path = isset($parts['path']) ? rtrim((string) $parts['path'], '/') : '';
        $query = isset($parts['query']) && $parts['query'] !== '' ? '?' . $parts['query'] : '';

        return $scheme . '://' . $host . $path . $query;
    }
}
