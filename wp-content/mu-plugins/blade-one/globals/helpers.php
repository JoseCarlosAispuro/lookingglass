<?php
/**
 * Global functions
 * Plugin Name:    BladeOne View
 * Plugin URI:     https://github.com/EFTEC/BladeOne
 * Description:    Replace Bladerunner with new blade view compiler. Laravel Blade template engine for WordPress
 * Version:        1.0.0
 * Author:         EkAndreas
 * Author URI:     https://www.elseif.se/
 * License:        MIT License
 * License URI:    http://opensource.org/licenses/MIT
 */

use eftec\bladeone\BladeOne;

require_once WPMU_PLUGIN_DIR . '/blade-one/lib/BladeOne.php';

if (!function_exists('view')) {
    /**
     * require:
     * -    templatePath: Blade file
     * -    data: Any Params....
     */
    function view($templatePath, $data = [])
    {
        $views = path_join(TEMPLATEPATH, 'views');
        $cache = path_join(ABSPATH, join(DIRECTORY_SEPARATOR, ["wp-content", "uploads", "cache"]));

        if (!file_exists($cache)) {
            mkdir($cache);
        }

        try {
            $blade = new BladeOne($views, $cache, BladeOne::MODE_AUTO);
            $blade->setBaseUrl(get_site_url() . "/wp-content/themes/fantom-magazine/dist/");

            return $blade->run($templatePath, $data);
        } catch (Exception $e) {
            return "error found " . $e->getMessage() . "<br>" . $e->getTraceAsString();
        }
    }
}
