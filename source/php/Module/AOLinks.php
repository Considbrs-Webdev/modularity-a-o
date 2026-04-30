<?php

declare(strict_types=1);

namespace ModularityAOLinks\Module;

use ModularityAOLinks\Helper\AoIndexBuilder;
use ModularityAOLinks\Helper\CacheBust;
use ModularityAOLinks\Helper\ManualLinkRowHelper;
use ModularityAOLinks\Helper\PageTreeBuilder;

/**
 * A-Ö alphabetical links module with server-rendered page tree and manual entries.
 */
class AOLinks extends \Modularity\Module
{
    public $slug = 'a-o';

    public $supports = [];

    public function init(): void
    {
        $this->nameSingular = __('A-Ö Links', 'modularity-a-o');
        $this->namePlural = __('A-Ö Links', 'modularity-a-o');
        $this->description = __('Alphabetical links from a page tree plus manual entries.', 'modularity-a-o');
    }

    public function data(): array
    {
        $fields = $this->getFields();
        $rootPageId = $this->resolveRootPageId($fields);
        $manualLinks = $this->buildManualLinksPayload($fields['links'] ?? []);
        $instanceId = $this->getID();
        $instanceKey = $instanceId !== null ? (string) $instanceId : uniqid('mod-a-o-', false);

        $apiPages = $this->loadPublishedTreePages($rootPageId);
        $builder = new AoIndexBuilder();
        $merged = $builder->mergeLinks($apiPages, $manualLinks);
        $sorted = $builder->sortByLabelSv($merged);
        $sections = $builder->buildSections($instanceKey, $sorted);

        return [
            'instanceId' => $instanceKey,
            'hasLinks' => $sections !== [],
            'sections' => $sections,
            'i18n' => [
                'empty' => __('No links to display yet.', 'modularity-a-o'),
                'jumpLabel' => __('Jump to letter', 'modularity-a-o'),
            ],
            'rootStyle' => apply_filters('ModularityAOLinks/rootInlineStyle', '', $this),
        ];
    }

    public function template(): string
    {
        return 'a-o.blade.php';
    }

    public function style(): void
    {
        $styleFile = CacheBust::name('css/modularity-a-o.css');

        if ($styleFile) {
            wp_enqueue_style(
                'modularity-a-o',
                MODULARITY_A_O_URL . '/assets/dist/' . $styleFile,
                [],
                null
            );
        }
    }

    public function script(): void
    {
    }

    /**
     * @param array<string, mixed> $fields
     */
    private function resolveRootPageId(array $fields): int
    {
        $group = $fields['source_group'] ?? null;

        if (is_array($group) && ! empty($group['root_page'])) {
            return (int) $group['root_page'];
        }

        if (! empty($fields['source_group_root_page'])) {
            return (int) $fields['source_group_root_page'];
        }

        return 0;
    }

    /**
     * @return list<array{id: int, parentId: int, label: string, url: string}>
     */
    private function loadPublishedTreePages(int $rootPageId): array
    {
        if ($rootPageId <= 0) {
            return [];
        }

        $post = get_post($rootPageId);

        if (! $post || $post->post_type !== 'page' || $post->post_status !== 'publish') {
            return [];
        }

        return (new PageTreeBuilder())->buildPublishedDescendantsPayload($rootPageId);
    }

    /**
     * @param array<int, mixed> $rows
     * @return list<array{key: string, pageId: int, label: string, url: string, target: string}>
     */
    private function buildManualLinksPayload(array $rows): array
    {
        $out = [];
        $manualSeq = 0;

        foreach ($rows as $row) {
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
                    continue;
                }

                $label = isset($row['label']) ? trim((string) $row['label']) : '';

                if ($label === '') {
                    $label = get_the_title($pageId) ?: '';
                }

                $permalink = get_permalink($pageId);

                if (! is_string($permalink) || $permalink === '' || $label === '') {
                    continue;
                }

                $out[] = [
                    'key' => 'p' . $pageId,
                    'pageId' => $pageId,
                    'label' => $label,
                    'url' => $permalink,
                    'target' => '',
                ];

                continue;
            }

            $urlRaw = ManualLinkRowHelper::rawUrlFromRow($row);

            if ($urlRaw === '') {
                continue;
            }

            $label = isset($row['label']) ? trim((string) $row['label']) : '';

            if ($label === '') {
                $label = $urlRaw;
            }

            ++$manualSeq;
            $out[] = [
                'key' => 'm' . $manualSeq,
                'pageId' => 0,
                'label' => $label,
                'url' => $urlRaw,
                'target' => '',
            ];
        }

        return $out;
    }
}
