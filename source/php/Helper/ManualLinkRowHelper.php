<?php

declare(strict_types=1);

namespace ModularityAOLinks\Helper;

/**
 * Manual link repeater: row_type page|external (legacy rows inferred from data).
 */
class ManualLinkRowHelper
{
    public const ROW_PAGE = 'page';

    public const ROW_EXTERNAL = 'external';

    /**
     * @param array<string, mixed> $row
     */
    public static function resolveRowType(array $row): string
    {
        $legacy = isset($row['link_type']) ? (string) $row['link_type'] : '';

        if ($legacy === 'url') {
            return self::ROW_EXTERNAL;
        }

        if ($legacy === 'page') {
            return self::ROW_PAGE;
        }

        $rt = isset($row['row_type']) ? (string) $row['row_type'] : '';

        if ($rt === self::ROW_EXTERNAL || $rt === self::ROW_PAGE) {
            return $rt;
        }

        if (! empty($row['page']) && (int) $row['page'] > 0) {
            return self::ROW_PAGE;
        }

        if (self::rawUrlFromRow($row) !== '') {
            return self::ROW_EXTERNAL;
        }

        return self::ROW_PAGE;
    }

    /**
     * Row produces no link output.
     *
     * @param array<string, mixed> $row
     */
    public static function isEffectivelyEmpty(array $row): bool
    {
        $type = self::resolveRowType($row);

        if ($type === self::ROW_PAGE) {
            $pageId = isset($row['page']) ? (int) $row['page'] : 0;

            return $pageId <= 0;
        }

        return self::rawUrlFromRow($row) === '';
    }

    /**
     * URL subfield: ACF url (string) or legacy Link field (array with 'url' key).
     *
     * @param array<string, mixed> $row
     */
    public static function rawUrlFromRow(array $row): string
    {
        $value = $row['url'] ?? null;

        if (is_array($value) && isset($value['url'])) {
            return trim((string) $value['url']);
        }

        if (is_string($value)) {
            return trim($value);
        }

        return '';
    }
}
