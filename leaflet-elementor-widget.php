<?php
/*
Plugin Name: Leaflet Elementor Widget
Plugin URI: https://github.com/Joolace/leaflet-elementor
Description: Widget Leaflet per Elementor
Version: 1.2.0         
Author: Joolace    
Author URI: https://github.com/Joolace/
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

function check_for_leaflet_widget_update() {
    $response = wp_remote_get('https://api.github.com/repos/Joolace/leaflet-elementor/releases/latest');

    if (is_wp_error($response)) {
        error_log('Errore durante il controllo degli aggiornamenti del plugin Leaflet Elementor: ' . $response->get_error_message());
        return;
    }

    $release_data = json_decode(wp_remote_retrieve_body($response));
    $latest_version = $release_data->tag_name; 

    $latest_version = ltrim($latest_version, 'v');

    $current_version = get_option('leaflet_elementor_widget_version'); 

    if (version_compare($current_version, $latest_version, '<')) {
        $download_url = $release_data->zipball_url;
        update_leaflet_elementor_widget($download_url, $latest_version);
    }
}

function update_leaflet_elementor_widget($download_url, $latest_version) {
    $temp_file = download_url($download_url);
    if (is_wp_error($temp_file)) {
        error_log('Errore durante il download dell\'aggiornamento del plugin Leaflet Elementor: ' . $temp_file->get_error_message());
        return;
    }

    $unzip_result = unzip_file($temp_file, WP_PLUGIN_DIR);
    if (is_wp_error($unzip_result)) {
        error_log('Errore durante la decompressione dell\'aggiornamento del plugin Leaflet Elementor: ' . $unzip_result->get_error_message());
        unlink($temp_file); 
    }

    unlink($temp_file);

    update_option('leaflet_elementor_widget_version', $latest_version);
}

function activate_leaflet_elementor_widget() {
    update_option('leaflet_elementor_widget_version', '1.2.0'); 
}

register_activation_hook(__FILE__, 'activate_leaflet_elementor_widget');
add_action('admin_init', 'check_for_leaflet_widget_update');
