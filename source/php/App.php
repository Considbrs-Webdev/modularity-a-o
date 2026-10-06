<?php

namespace ModularityAOLinks;

use ModularityAOLinks\Admin\UniqueLinksValidator;
use ModularityAOLinks\Helper\CacheBust;

/**
 * Application bootstrap.
 */
class App
{
    public function __construct()
    {
        add_action('init', [$this, 'registerModule']);
        add_action('enqueue_block_assets', [$this, 'addEditorStyles']);
        add_filter('Pitea/Editor/ModuleStyles', [$this, 'registerEditorStyle']);

        new UniqueLinksValidator();
    }

    /**
     * Register the module with Modularity.
     *
     * @return void
     */
    public function registerModule(): void
    {
        if (function_exists('modularity_register_module')) {
            modularity_register_module(
                MODULARITY_A_O_MODULE_PATH,
                'AOLinks'
            );
        }
    }

    /**
     * Register the module stylesheet for the shared editor-canvas loader.
     *
     * @param array<string, string> $styles
     * @return array<string, string>
     */
    public function registerEditorStyle(array $styles): array
    {
        $url = $this->stylesheetUrl();
        if ($url !== '') {
            $styles['modularity-a-o'] = $url;
        }

        return $styles;
    }

    /**
     * Enqueue the module stylesheet inside the block editor iframe.
     *
     * @return void
     */
    public function addEditorStyles(): void
    {
        if (!is_admin() || wp_style_is('modularity-a-o', 'enqueued')) {
            return;
        }

        $url = $this->stylesheetUrl();
        if ($url === '') {
            return;
        }

        wp_enqueue_style('modularity-a-o', $url, [], null);
    }

    /**
     * Built stylesheet URL, or an empty string when the Vite manifest has no entry.
     */
    private function stylesheetUrl(): string
    {
        $styleFile = CacheBust::name('css/modularity-a-o.css');
        if (!$styleFile) {
            return '';
        }

        return MODULARITY_A_O_URL . '/assets/dist/' . $styleFile;
    }
}
