<?php
/*
Plugin Name:  CGStaticMaps
Plugin URI:   https://calguy1000.com/wordpress/CGStaticMaps
Description:  A simple plugin that creates a widget for adding static maps to a page
Version:      1.0
Author:       Robert Campbell (calguy1000)
License:      GPL2
*/
namespace Calguy1000\CGStaticMaps;
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

spl_autoload_register(__NAMESPACE__.'\\autoload');
function autoload( $classname ) {
    $cls = ltrim($classname, '\\');
    if(strpos($classname, __NAMESPACE__) !== 0) return;

    $cls = str_replace(__NAMESPACE__, '', $classname);
    $path = __DIR__ . '/lib' .str_replace('\\', DIRECTORY_SEPARATOR, $cls) . '.php';
    require_once($path);
}

// and kickstart the plugin.
\Calguy1000\CGStaticMaps\plugin::get_instance();
