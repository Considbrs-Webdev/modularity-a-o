<?php

namespace ModularityAOLinks\Helper;

/**
 * Resolves hashed asset paths from the Vite manifest.
 */
class CacheBust
{
    private static ?array $manifest = null;

    /**
     * @param string $name Logical asset key from manifest (e.g. css/modularity-a-o.css).
     * @return string|false
     */
    public static function name(string $name): string|false
    {
        $manifest = self::getManifest();

        if ($manifest && isset($manifest[$name])) {
            return $manifest[$name];
        }

        return false;
    }

    /**
     * @return array|null
     */
    private static function getManifest(): ?array
    {
        if (self::$manifest !== null) {
            return self::$manifest;
        }

        $jsonPath = MODULARITY_A_O_PATH . apply_filters(
            'ModularityAOLinks/Helper/CacheBust/RevManifestPath',
            'assets/dist/manifest.json'
        );

        if (file_exists($jsonPath)) {
            $decoded = json_decode((string) file_get_contents($jsonPath), true);
            self::$manifest = is_array($decoded) ? $decoded : null;

            return self::$manifest;
        }

        return null;
    }
}
