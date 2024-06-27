<?php
/**
 * Plugin Name: Leaflet Elementor Widget
 * Plugin URI: https://github.com/Joolace/leaflet-elementor
 * Description: Widget Leaflet for Elementor
 * Author: joolace
 * Author URI: https://github.com/Joolace/
 * Text Domain: leaflet-elementor-widget
 * Version: 1.2.7
 * License: GPL2
 * Leaflet Elementor Widget is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * Leaflet Elementor Widget is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Leaflet Elementor Widget. If not, see  https://github.com/Joolace/leaflet-elementor/blob/main/LICENSE.
 */

function load_leaflet_elementor_widget_scripts() {
    wp_enqueue_style('leaflet-css', 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.css');
    wp_enqueue_script('leaflet-js', 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.js', array(), null, true);
    wp_enqueue_script('leaflet-fullscreen-js', 'https://cdnjs.cloudflare.com/ajax/libs/leaflet.fullscreen/3.0.2/Control.FullScreen.min.js', array('leaflet-js'), null, true);
    wp_enqueue_style('leaflet-fullscreen-css', 'https://cdnjs.cloudflare.com/ajax/libs/leaflet.fullscreen/3.0.2/Control.FullScreen.min.css');
    wp_enqueue_script('leaflet-routing-machine', plugin_dir_url( __FILE__ ) . 'js/leaflet-routing-machine.js', array('leaflet-js'), '3.2.12', true);
    wp_enqueue_style('leaflet-routing-machine-css', plugin_dir_url( __FILE__ ) . 'css/leaflet-routing-machine.css', array(), '3.2.12');

    $locale_file = plugin_dir_url(__FILE__) . 'js/localization.js';
    if (file_exists($locale_file)) {
        wp_enqueue_script('leaflet-routing-machine-localization', $locale_file, array('leaflet-routing-machine'), '3.2.12', true);
    }
}
add_action('wp_enqueue_scripts', 'load_leaflet_elementor_widget_scripts');

function register_leaflet_elementor_widget() {
    require_once( plugin_dir_path( __FILE__ ) . 'class-widget-leaflet-elementor.php' );
    require_once( plugin_dir_path( __FILE__ ) . 'widget-leaflet-map.php' );
    require_once( plugin_dir_path( __FILE__ ) . 'multimarker-elementor-widget.php' );

    \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Leaflet_Elementor_Widget() );
    \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new LeafLetRoutingPlugin\Leaflet_Map_Widget() );
    \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new MultiMarker_Elementor_Widget() );
}
add_action( 'elementor/widgets/widgets_registered', 'register_leaflet_elementor_widget' );