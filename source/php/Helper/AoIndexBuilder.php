<?php

declare(strict_types=1);

namespace ModularityAOLinks\Helper;

use Collator;

/**
 * Merges API page rows with manual links, sorts (sv-SE), groups by first letter (parity with former JS).
 */
final class AoIndexBuilder
{
    /** @var list<string> */
    private const LETTER_ORDER = [
        'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M',
        'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
        'Å', 'Ä', 'Ö', '0-9', '#',
    ];

    /**
     * @param list<array{id: int, parentId?: int, label: string, url: string}|array<string, mixed>> $apiPages
     * @param list<array{key: string, pageId: int, label: string, url: string, target: string}> $manualLinks
     * @return list<array{key: string, pageId: int, label: string, url: string, target: string}>
     */
    public function mergeLinks(array $apiPages, array $manualLinks): array
    {
        /** @var array<string, array{key: string, pageId: int, label: string, url: string, target: string}> $map */
        $map = [];

        foreach ($apiPages as $p) {
            if (! is_array($p)) {
                continue;
            }

            $id = (int) ($p['id'] ?? 0);

            if ($id <= 0) {
                continue;
            }

            $key = 'p' . $id;
            $map[$key] = [
                'key' => $key,
                'pageId' => $id,
                'label' => isset($p['label']) ? (string) $p['label'] : '',
                'url' => isset($p['url']) ? (string) $p['url'] : '',
                'target' => '',
            ];
        }

        foreach ($manualLinks as $m) {
            if (! is_array($m)) {
                continue;
            }

            $pageId = (int) ($m['pageId'] ?? 0);

            if ($pageId > 0) {
                $key = 'p' . $pageId;
                $base = $map[$key] ?? [
                    'key' => $key,
                    'pageId' => $pageId,
                    'label' => '',
                    'url' => '',
                    'target' => '',
                ];

                $mLabel = isset($m['label']) ? trim((string) $m['label']) : '';
                $mUrl = isset($m['url']) ? trim((string) $m['url']) : '';
                $mTarget = isset($m['target']) ? trim((string) $m['target']) : '';

                $label = $mLabel !== '' ? (string) ($m['label'] ?? '') : $base['label'];
                $url = $mUrl !== '' ? (string) ($m['url'] ?? '') : $base['url'];
                $target = $mTarget !== '' ? (string) $m['target'] : ($base['target'] ?? '');

                $map[$key] = [
                    'key' => $key,
                    'pageId' => $pageId,
                    'label' => $label,
                    'url' => $url,
                    'target' => $target,
                ];
            } else {
                $key = (string) ($m['key'] ?? '');
                if ($key === '') {
                    continue;
                }

                $map[$key] = [
                    'key' => $key,
                    'pageId' => 0,
                    'label' => isset($m['label']) ? (string) $m['label'] : '',
                    'url' => isset($m['url']) ? (string) $m['url'] : '',
                    'target' => isset($m['target']) ? (string) $m['target'] : '',
                ];
            }
        }

        $out = [];

        foreach ($map as $row) {
            $lab = trim($row['label']);
            $u = trim($row['url']);

            if ($lab !== '' && $u !== '') {
                $out[] = [
                    'key' => $row['key'],
                    'pageId' => (int) $row['pageId'],
                    'label' => $lab,
                    'url' => $u,
                    'target' => trim($row['target'] ?? ''),
                ];
            }
        }

        return $out;
    }

    /**
     * @param list<array{key: string, pageId: int, label: string, url: string, target: string}> $items
     * @return list<array{key: string, pageId: int, label: string, url: string, target: string}>
     */
    public function sortByLabelSv(array $items): array
    {
        if ($items === []) {
            return [];
        }

        $collator = class_exists(Collator::class)
            ? new Collator('sv_SE')
            : null;

        if ($collator instanceof Collator) {
            $collator->setStrength(Collator::PRIMARY);
            // NUMERIC_ORDERING exists from PHP 7.4+ with intl; some builds omit the constant.
            $numericOrdering = Collator::class . '::NUMERIC_ORDERING';
            $on = Collator::class . '::ON';
            if (defined($numericOrdering) && defined($on)) {
                $collator->setAttribute(constant($numericOrdering), constant($on));
            }
        }

        usort($items, static function (array $a, array $b) use ($collator): int {
            $la = $a['label'];
            $lb = $b['label'];

            if ($collator instanceof Collator) {
                $cmp = $collator->compare($la, $lb);

                return $cmp !== false ? $cmp : strcmp($la, $lb);
            }

            return strcasecmp($la, $lb);
        });

        return $items;
    }

    public function firstLetterKey(string $label): string
    {
        if ($label === '') {
            return '#';
        }

        $first = mb_substr($label, 0, 1, 'UTF-8');

        if ($first === '') {
            return '#';
        }

        $upper = mb_strtoupper($first, 'UTF-8');

        if (strlen($upper) === 1 && $upper >= 'A' && $upper <= 'Z') {
            return $upper;
        }

        if ($upper === 'Å' || $upper === 'Ä' || $upper === 'Ö') {
            return $upper;
        }

        if (strlen($upper) === 1 && ctype_digit($upper)) {
            return '0-9';
        }

        return '#';
    }

    public function letterToAnchor(string $letter): string
    {
        $map = [
            'Å' => 'letter-aa',
            'Ä' => 'letter-ae',
            'Ö' => 'letter-oe',
            '#' => 'letter-other',
            '0-9' => 'letter-09',
        ];

        if (isset($map[$letter])) {
            return $map[$letter];
        }

        if (strlen($letter) === 1 && $letter >= 'A' && $letter <= 'Z') {
            return 'letter-' . strtolower($letter);
        }

        return 'letter-misc';
    }

    public function letterRank(string $letter): int
    {
        $idx = array_search($letter, self::LETTER_ORDER, true);

        return $idx === false ? 1000 : (int) $idx;
    }

    /**
     * @param list<array{key: string, pageId: int, label: string, url: string, target: string}> $sortedItems
     * @return list<array{
     *     letter: string,
     *     anchor: string,
     *     sectionId: string,
     *     headingId: string,
     *     items: list<array{label: string, href: string, target: string}>
     * }>
     */
    public function buildSections(string $instanceId, array $sortedItems): array
    {
        if ($sortedItems === []) {
            return [];
        }

        /** @var array<string, list<array{key: string, pageId: int, label: string, url: string, target: string}>> $groups */
        $groups = [];

        foreach ($sortedItems as $item) {
            $letter = $this->firstLetterKey($item['label']);

            if (! isset($groups[$letter])) {
                $groups[$letter] = [];
            }

            $groups[$letter][] = $item;
        }

        $letters = array_keys($groups);

        usort($letters, function (string $a, string $b): int {
            $ra = $this->letterRank($a);
            $rb = $this->letterRank($b);

            if ($ra !== $rb) {
                return $ra <=> $rb;
            }

            return strcmp($a, $b);
        });

        $safeId = preg_replace('/[^a-zA-Z0-9_-]/', '-', $instanceId) ?: 'instance';

        $sections = [];

        foreach ($letters as $letter) {
            $anchor = $this->letterToAnchor($letter);
            $suffix = $safeId . '-' . $anchor;
            $sectionId = 'mod-a-o-' . $suffix;
            $headingId = 'mod-a-o-heading-' . $suffix;

            $listingItems = [];

            foreach ($groups[$letter] as $row) {
                $listingItems[] = [
                    'label' => $row['label'],
                    'href' => $row['url'],
                    'target' => $row['target'],
                ];
            }

            $sections[] = [
                'letter' => $letter,
                'anchor' => $anchor,
                'sectionId' => $sectionId,
                'headingId' => $headingId,
                'items' => $listingItems,
            ];
        }

        return $sections;
    }
}
