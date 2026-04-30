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
        add_action('enqueue_block_editor_assets', [$this, 'addEditorStyles']);

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
     * Enqueue built CSS in the block editor.
     *
     * @return void
     */
    public function addEditorStyles(): void
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
}
