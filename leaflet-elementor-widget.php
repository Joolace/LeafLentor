<?php
/*
Plugin Name: Leaflet Elementor Widget
Plugin URI: https://github.com/Joolace/leaflet-elementor
Description: Widget Leaflet per Elementor
Version: 1.2.6         
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

    $current_version = get_option('leaflet_elementor_widget_version');

    if (version_compare($current_version, $latest_version, '<')) {
        add_action( 'admin_notices', 'leaflet_elementor_update_notice' );
    }
}

function leaflet_elementor_update_notice() {
    $response = wp_remote_get('https://api.github.com/repos/Joolace/leaflet-elementor/releases/latest');
    if (!is_wp_error($response)) {
        $release_data = json_decode(wp_remote_retrieve_body($response));
        $latest_version = $release_data->tag_name;
        ?>
        <div class="notice notice-warning is-dismissible">
            <p>È disponibile una nuova versione del plugin Leaflet Elementor Widget (<?php echo $latest_version; ?>). 
            <a href="<?php echo admin_url('options-general.php?page=leaflet-elementor-widget-settings'); ?>" class="button button-primary">Aggiorna ora</a></p>
        </div>
        <?php
    }
}

function update_leaflet_elementor_widget($download_url, $latest_version) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

    $skin = new WP_Ajax_Upgrader_Skin();
    $upgrader = new Plugin_Upgrader($skin);
    $result = $upgrader->install($download_url);

    if (is_wp_error($result)) {
        error_log('Errore durante l\'installazione dell\'aggiornamento del plugin Leaflet Elementor: ' . $result->get_error_message());
        echo '<div class="notice notice-error"><p>Si è verificato un errore durante l\'aggiornamento del plugin.</p></div>';
    } else {
        update_option('leaflet_elementor_widget_version', $latest_version);
        activate_plugin(plugin_basename(__FILE__));
        echo '<div class="notice notice-success is-dismissible"><p>Il plugin Leaflet Elementor Widget è stato aggiornato alla versione ' . $latest_version . '.</p></div>';
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
    if (isset($_POST['update_plugin'])) {
        check_for_leaflet_widget_update(true); 
    }

    echo '<div class="wrap">';
    echo '<h1>Impostazioni Leaflet Elementor Widget</h1>';
    echo '<form method="post" action="options.php">';

    settings_fields('leaflet_elementor_widget_update_settings');
    do_settings_sections('leaflet-elementor-widget-settings');

    echo '<input type="submit" name="update_plugin" class="button button-primary" value="Aggiorna ora">';
    echo '</form>';
    echo '</div>';
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
