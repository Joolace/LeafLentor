<?php
/*
Plugin Name: Leaflet Elementor Widget
Plugin URI: https://github.com/Joolace/leaflet-elementor
Description: Widget Leaflet per Elementor
Version: 1.2.5         
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
    if (!get_option('enable_auto_update')) {
        return;
    }

    $response = wp_remote_get('https://api.github.com/repos/Joolace/leaflet-elementor/releases/latest');

    if (is_wp_error($response)) {
        error_log('Errore durante il controllo degli aggiornamenti del plugin Leaflet Elementor: ' . $response->get_error_message());
        return;
    }

    $release_data = json_decode(wp_remote_retrieve_body($response));

    if (empty($release_data) || !is_object($release_data)) {
        error_log('Nessuna release trovata per il plugin Leaflet Elementor Widget.');
        return;
    }

    $latest_version = $release_data->tag_name;

    $current_version = get_option('leaflet_elementor_widget_version');

    if (version_compare($current_version, $latest_version, '<')) {
        $download_url = $release_data->zipball_url; 
        update_leaflet_elementor_widget($download_url, $latest_version);
    }
}

function update_leaflet_elementor_widget($download_url, $latest_version) {
    require_once ABSPATH . 'wp-admin/includes/plugin-install.php';

    $result = install_plugin_from_zip($download_url);

    if (is_wp_error($result)) {
        error_log('Errore durante l\'installazione dell\'aggiornamento del plugin Leaflet Elementor: ' . $result->get_error_message());
    } else {
        update_option('leaflet_elementor_widget_version', $latest_version);

        if (!is_plugin_active(plugin_basename(__FILE__))) {
            activate_plugin(plugin_basename(__FILE__));
        }
    }
}

function activate_leaflet_elementor_widget() {
    update_option('leaflet_elementor_widget_version', '1.2.5');
}

function leaflet_elementor_widget_options_page() {
    add_options_page(
        'Impostazioni Leaflet Elementor Widget', 
        'Leaflet Elementor Widget', 
        'manage_options', 
        'leaflet-elementor-widget-settings', 
        'leaflet_elementor_widget_settings_page_content' 
    );
}

function leaflet_elementor_widget_settings_page_content() {
    ?>
    <div class="wrap">
        <h1>Impostazioni Leaflet Elementor Widget</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('leaflet_elementor_widget_update_settings');
            do_settings_sections('leaflet-elementor-widget-settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

function leaflet_elementor_widget_settings() {
    add_settings_section(
        'leaflet_elementor_widget_update_settings',
        'Aggiornamento Automatico',
        'leaflet_elementor_widget_update_settings_callback',
        'leaflet-elementor-widget-settings' 
    );

    add_settings_field(
        'enable_auto_update',
        'Abilita Aggiornamento Automatico',
        'enable_auto_update_callback',
        'leaflet-elementor-widget-settings',
        'leaflet_elementor_widget_update_settings'
    );

    register_setting('leaflet_elementor_widget_update_settings', 'enable_auto_update');
}

function enable_auto_update_callback() {
    $option = get_option('enable_auto_update');
    echo '<input type="checkbox" id="enable_auto_update" name="enable_auto_update" value="1" ' . checked(1, $option, false) . ' />';
}

function leaflet_elementor_widget_update_settings_callback() {
    echo '<p>Controlla se vuoi abilitare l\'aggiornamento automatico del plugin.</p>';
}

register_activation_hook(__FILE__, 'activate_leaflet_elementor_widget');
add_action('admin_init', 'leaflet_elementor_widget_settings');
add_action('admin_init', 'check_for_leaflet_widget_update');
add_action('admin_menu', 'leaflet_elementor_widget_options_page');
