<?php

/**
 * Plugin Name:       Modularity A-Ö Links
 * Plugin URI:        https://github.com/considbrs-webdev/modularity-a-o
 * Description:       Alphabetical (A-Ö) link lists for Modularity with optional page-tree import.
 * Version:           1.0.0
 * Author:            Consid Borås AB
 * Author URI:        https://github.com/Considbrs-Webdev
 * License:           MIT
 * License URI:       https://opensource.org/licenses/MIT
 * Text Domain:       modularity-a-o
 * Domain Path:       /languages
 */

if (! defined('WPINC')) {
    die;
}

define('MODULARITY_A_O_PATH', plugin_dir_path(__FILE__));
define('MODULARITY_A_O_URL', plugins_url('', __FILE__));
define('MODULARITY_A_O_MODULE_VIEW_PATH', plugin_dir_path(__FILE__) . 'source/php/Module/views');
define('MODULARITY_A_O_MODULE_PATH', MODULARITY_A_O_PATH . 'source/php/Module/');

add_action('init', function () {
    load_plugin_textdomain('modularity-a-o', false, plugin_basename(dirname(__FILE__)) . '/languages');
});

if (file_exists(MODULARITY_A_O_PATH . 'vendor/autoload.php')) {
    require_once MODULARITY_A_O_PATH . 'vendor/autoload.php';
}

add_action('acf/init', function () {
    $acfExportManager = new \AcfExportManager\AcfExportManager();
    $acfExportManager->setTextdomain('modularity-a-o');
    $acfExportManager->setExportFolder(MODULARITY_A_O_PATH . 'source/php/AcfFields/');
    $acfExportManager->autoExport(array(
        'a-o-module' => 'group_7c1d2e3f4a5b6',
    ));
    $acfExportManager->import();
});

add_filter('/Modularity/externalViewPath', function ($arr) {
    $arr['mod-a-o'] = MODULARITY_A_O_MODULE_VIEW_PATH;

    return $arr;
}, 10, 3);

new ModularityAOLinks\App();
