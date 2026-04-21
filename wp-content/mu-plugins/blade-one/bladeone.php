<?php
/**
 * Plugin Name: BladeOne
 * Plugin URI: https://github.com/EFTEC/BladeOne
 * Description: Building on BladeOne's and WordPress's strengths, together.
 * Version: 0.1
 * Author: Andreas Ek
 * Author URI: https://gist.github.com/ekandreas/0f750af15745baf7826cb2dd4d32cb56
 *
 * @package BladeOne
 */

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

/**
 * Register global files and functions
 */
array_map(function ($file) {
    $file = "globals/{$file}.php";
    require_once($file);
}, ['helpers']);