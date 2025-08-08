<?php
/**
 * Plugin Name: LeafLentor
 * Plugin URI: https://github.com/Joolace/LeafLentor
 * Description: Leaflet Map widgets for Elementor.
 * Author: joolace
 * Author URI: https://github.com/Joolace/
 * Version: 1.4.0
 * Text Domain: leaflentor
 * Domain Path: /languages
 * License: GPLv2 or later
 *
 * LeafLentor is free software: you can redistribute it and/or modify it under the terms of the GPLv2 or later.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// === Constants ===
define( 'LEAFLENTOR_VERSION', '1.3.3' );
define( 'LEAFLENTOR_FILE', __FILE__ );
define( 'LEAFLENTOR_DIR', plugin_dir_path( __FILE__ ) );
define( 'LEAFLENTOR_URL', plugin_dir_url( __FILE__ ) );

add_action( 'plugins_loaded', function () {
    if ( ! did_action( 'elementor/loaded' ) ) {
        return;
    }

    if ( defined( 'ELEMENTOR_VERSION' ) && version_compare( ELEMENTOR_VERSION, '3.5.0', '>=' ) ) {
        add_action( 'elementor/widgets/register', 'leaflentor_register_widgets_new' );
    } else {
        add_action( 'elementor/widgets/widgets_registered', 'leaflentor_register_widgets_legacy' );
    }
} );

add_action( 'wp_enqueue_scripts', function () {

	$assets = [
		'leaflet-css'  => [ 'path' => 'assets/leaflet/leaflet.css' ],
		'leaflet-js'   => [ 'path' => 'assets/leaflet/leaflet.js', 'deps' => [], 'footer' => true ],

		'leaflet-fs-css' => [ 'path' => 'assets/leaflet-fullscreen/Control.FullScreen.css' ],
		'leaflet-fs-js'  => [ 'path' => 'assets/leaflet-fullscreen/Control.FullScreen.js', 'deps' => [ 'leaflet-js' ], 'footer' => true ],

		'lrm-css' => [ 'path' => 'assets/leaflet-routing-machine/leaflet-routing-machine.css' ],
		'lrm-js'  => [ 'path' => 'assets/leaflet-routing-machine/leaflet-routing-machine.js', 'deps' => [ 'leaflet-js' ], 'footer' => true ],
	];

	foreach ( $assets as $handle => $conf ) {
		$file = LEAFLENTOR_DIR . $conf['path'];
		$url  = LEAFLENTOR_URL . $conf['path'];

		if ( file_exists( $file ) ) {
			$ver = LEAFLENTOR_VERSION;
			$mtime = @filemtime( $file );
            if ( $mtime ) {
               $ver = $mtime; 
            }

			if ( preg_match( '/\.css$/', $conf['path'] ) ) {
				wp_enqueue_style( $handle, $url, [], $ver );
			} else {
				wp_enqueue_script( $handle, $url, $conf['deps'] ?? [], $ver, ! empty( $conf['footer'] ) );
			}
		}
	}

	$locale_js_path = LEAFLENTOR_DIR . 'assets/leaflet-routing-machine/localization.js';
	if ( file_exists( $locale_js_path ) ) {
		wp_enqueue_script(
			'lrm-localization',
			LEAFLENTOR_URL . 'assets/leaflet-routing-machine/localization.js',
			[ 'lrm-js' ],
			(string) @filemtime( $locale_js_path ),
			true
		);
	}
} );

add_action( 'admin_notices', function () {
    if ( ! current_user_can( 'activate_plugins' ) ) return;
    if ( did_action( 'elementor/loaded' ) ) return;

    echo '<div class="notice notice-warning"><p>' .
        esc_html__( 'LeafLentor requires Elementor to be installed and active.', 'leaflentor' ) .
    '</p></div>';
} );


function leaflentor_register_widgets_new( $widgets_manager ) {
	require_once LEAFLENTOR_DIR . 'class-widget-leaflet-elementor.php';
	require_once LEAFLENTOR_DIR . 'widget-leaflet-map.php';
	require_once LEAFLENTOR_DIR . 'multimarker-elementor-widget.php';

	if ( class_exists( '\Leaflet_Elementor_Widget' ) ) {
		$widgets_manager->register( new \Leaflet_Elementor_Widget() );
	}
	if ( class_exists( '\LeafLetRoutingPlugin\Leaflet_Map_Widget' ) ) {
		$widgets_manager->register( new \LeafLetRoutingPlugin\Leaflet_Map_Widget() );
	}
	if ( class_exists( '\MultiMarker_Elementor_Widget' ) ) {
		$widgets_manager->register( new \MultiMarker_Elementor_Widget() );
	}
}

function leaflentor_register_widgets_legacy() {
	require_once LEAFLENTOR_DIR . 'class-widget-leaflet-elementor.php';
	require_once LEAFLENTOR_DIR . 'widget-leaflet-map.php';
	require_once LEAFLENTOR_DIR . 'multimarker-elementor-widget.php';

	if ( class_exists( '\Elementor\Plugin' ) ) {
		if ( class_exists( '\Leaflet_Elementor_Widget' ) ) {
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Leaflet_Elementor_Widget() );
		}
		if ( class_exists( '\LeafLetRoutingPlugin\Leaflet_Map_Widget' ) ) {
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \LeafLetRoutingPlugin\Leaflet_Map_Widget() );
		}
		if ( class_exists( '\MultiMarker_Elementor_Widget' ) ) {
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \MultiMarker_Elementor_Widget() );
		}
	}
}