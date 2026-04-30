<?php

declare(strict_types=1);

namespace ModularityAOLinks\Admin;

use ModularityAOLinks\Helper\ManualLinkRowHelper;
use ModularityAOLinks\Helper\UrlNormalizer;

/**
 * Validates manual link rows by row_type: Page vs External URL.
 */
class UniqueLinksValidator
{
    public const LINKS_REPEATER_KEY = 'field_7c1d2e3f4a5b61';

    public function __construct()
    {
        add_filter(
            'acf/validate_value/key=' . self::LINKS_REPEATER_KEY,
            [$this, 'validate'],
            10,
            4
        );
    }

    /**
     * @param bool|string $valid
     * @param mixed $value
     * @param array<string, mixed> $field
     * @param string $input
     * @return bool|string
     */
    public function validate($valid, $value, $field, $input)
    {
        if ($valid !== true) {
            return $valid;
        }

        if (! is_array($value) || $value === []) {
            return $valid;
        }

        $seenPageIds = [];
        $seenUrls = [];

        foreach ($value as $row) {
            if (! is_array($row)) {
                continue;
            }

            if (ManualLinkRowHelper::isEffectivelyEmpty($row)) {
                continue;
            }

            $rowType = ManualLinkRowHelper::resolveRowType($row);

            if ($rowType === ManualLinkRowHelper::ROW_PAGE) {
                $pageId = isset($row['page']) ? (int) $row['page'] : 0;

                if ($pageId <= 0) {
                    return __('Page rows require a selected page.', 'modularity-a-o');
                }

                if (isset($seenPageIds[$pageId])) {
                    return __('This page has already been selected.', 'modularity-a-o');
                }

                $seenPageIds[$pageId] = true;

                continue;
            }

            $url = ManualLinkRowHelper::rawUrlFromRow($row);

            if ($url === '') {
                return __('External URL rows need a URL.', 'modularity-a-o');
            }

            $norm = UrlNormalizer::normalize($url);

            if ($norm === '') {
                return __('External URL must be valid.', 'modularity-a-o');
            }

            if (isset($seenUrls[$norm])) {
                return __('This URL is already used in another external link row.', 'modularity-a-o');
            }

            $seenUrls[$norm] = true;
        }

        return $valid;
    }
}
