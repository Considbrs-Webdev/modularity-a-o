<?php

declare(strict_types=1);

namespace ModularityAOLinks\Helper;

use WP_Post;

/**
 * Collects published page descendants for a root page.
 */
class PageTreeBuilder
{
    /**
     * @return list<int>
     */
    public function collectDescendantIds(int $rootId): array
    {
        if ($rootId <= 0) {
            return [];
        }

        $out = [];

        foreach ($this->directPublishedChildIds($rootId) as $childId) {
            $out[] = $childId;
            foreach ($this->collectDescendantIds($childId) as $desc) {
                $out[] = $desc;
            }
        }

        return $out;
    }

    /**
     * @return list<array{id: int, parentId: int, label: string, url: string}>
     */
    public function buildPublishedDescendantsPayload(int $rootId): array
    {
        $ids = $this->collectDescendantIds($rootId);

        if ($ids === []) {
            return [];
        }

        $posts = [];

        foreach ($ids as $pageId) {
            $post = get_post($pageId);

            if (! $post instanceof WP_Post || $post->post_status !== 'publish' || $post->post_type !== 'page') {
                continue;
            }

            $posts[] = $post;
        }

        usort($posts, static function (WP_Post $a, WP_Post $b) {
            return strcasecmp($a->post_title, $b->post_title);
        });

        $rows = [];

        foreach ($posts as $page) {
            $url = get_permalink($page);

            if (! is_string($url) || $url === '') {
                continue;
            }

            $rows[] = [
                'id' => (int) $page->ID,
                'parentId' => (int) $page->post_parent,
                'label' => get_the_title($page),
                'url' => $url,
            ];
        }

        return $rows;
    }

    /**
     * @return list<int>
     */
    private function directPublishedChildIds(int $parentId): array
    {
        $children = get_posts([
            'post_type' => 'page',
            'post_status' => 'publish',
            'post_parent' => $parentId,
            'posts_per_page' => -1,
            'fields' => 'ids',
            'orderby' => 'title',
            'order' => 'ASC',
            'no_found_rows' => true,
        ]);

        return is_array($children) ? array_map('intval', $children) : [];
    }
}
