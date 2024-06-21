<?php
/*
Plugin Name: Leaflet Elementor Widget
Plugin URI: https://github.com/Joolace/leaflet-elementor
Description: Widget Leaflet per Elementor
Version: 1.2.6         
Author: Joolace    
Author URI: https://github.com/Joolace/
*/

if ( !class_exists('gwplgUpdateChecker_wdg') ) {
    class gwplgUpdateChecker_wdg{
        public $plugin_slug;
        public $version;
        public $cache_key;
        public $cache_allowed;
        public function __construct() {
            $this->plugin_slug = plugin_basename( __DIR__ );
            $this->version = '0.21';
            $this->cache_key = 'customwidgets_updater';
            $this->cache_allowed = true;
            add_filter( 'plugins_api', array( $this, 'info' ), 20, 3 );
            add_filter( 'site_transient_update_plugins', array( $this, 'update' ) );
            add_action( 'upgrader_process_complete', array( $this, 'purge' ), 10, 2 );
        }
        public function request() {
            $remote = get_transient( $this->cache_key );
            if( false === $remote || ! $this->cache_allowed ) {
                $remote = wp_remote_get(
                    'https://github.com/Joolace/leaflet-elementor/blob/main/upd-elementor-widget.json',
                    array(
                        'timeout' => 10,
                        'headers' => array(
                            'Accept' => 'application/json'
                        )
                    )
                );
                if(
                    is_wp_error( $remote )
                    || 200 !== wp_remote_retrieve_response_code( $remote )
                    || empty( wp_remote_retrieve_body( $remote ) )
                ) {
                    return false;
                }
                set_transient( $this->cache_key, $remote, DAY_IN_SECONDS );
            }
            $remote = json_decode( wp_remote_retrieve_body( $remote ) );
            return $remote;
        }
        function info( $res, $action, $args ) {
            // do nothing if you're not getting plugin information right now
            if( 'plugin_information' !== $action ) {
                return $res;
            }
            // do nothing if it is not our plugin
            if( $this->plugin_slug !== $args->slug ) {
                return $res;
            }
            // get updates
            $remote = $this->request();
            if( ! $remote ) {
                return $res;
            }
            $res = new stdClass();
            $res->name = $remote->name;
            $res->slug = $remote->slug;
            $res->version = $remote->version;
            $res->tested = $remote->tested;
            $res->requires = $remote->requires;
            $res->author = $remote->author;
            $res->author_profile = $remote->author_profile;
            $res->download_link = $remote->download_url;
            $res->trunk = $remote->download_url;
            $res->requires_php = $remote->requires_php;
            $res->last_updated = $remote->last_updated;
            $res->sections = array(
                'description' => $remote->sections->description,
                'installation' => $remote->sections->installation,
                'changelog' => $remote->sections->changelog
            );
            if( ! empty( $remote->banners ) ) {
                $res->banners = array(
                    'low' => $remote->banners->low,
                    'high' => $remote->banners->high
                );
            }
            return $res;
        }
        public function update( $transient ) {
            if ( empty($transient->checked ) ) {
                return $transient;
            }
            $remote = $this->request();
            if(
                $remote
                && version_compare( $this->version, $remote->version, '<' )
                && version_compare( $remote->requires, get_bloginfo( 'version' ), '<=' )
                && version_compare( $remote->requires_php, PHP_VERSION, '<' )
            ) {
                $res = new stdClass();
                $res->slug = $this->plugin_slug;
                $res->plugin = plugin_basename( __FILE__ ); // example: misha-update-plugin/misha-update-plugin.php
                $res->new_version = $remote->version;
                $res->tested = $remote->tested;
                $res->package = $remote->download_url;
                $transient->response[ $res->plugin ] = $res;
            }
            return $transient;
        }
        public function purge( $upgrader, $options ) {
            if (
                $this->cache_allowed
                && 'update' === $options['action']
                && 'plugin' === $options[ 'type' ]
            ) {
                // just clean the cache when new plugin version is installed
                delete_transient( $this->cache_key );
            }
        }
    }
    new gwplgUpdateChecker_wdg();
}
add_filter( 'plugin_row_meta', function( $links_array, $plugin_file_name, $plugin_data, $status ) {
    if( strpos( $plugin_file_name, basename( __FILE__ ) ) ) {
        $links_array[] = sprintf(
            '<a href="%s" class="thickbox open-plugin-details-modal">%s</a>',
            add_query_arg(
                array(
                    'tab' => 'plugin-information',
                    'plugin' => plugin_basename( __DIR__ ),
                    'TB_iframe' => true,
                    'width' => 772,
                    'height' => 788
                ),
                admin_url( 'plugin-install.php' )
            ),
            __( 'View details' )
        );
    }
    return $links_array;
}, 25, 4 );

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


function check_for_leaflet_widget_update($force_update = false) {
    $response = wp_remote_get('https://api.github.com/repos/Joolace/leaflet-elementor/releases/latest');

    if (is_wp_error($response)) {
        error_log('Errore durante il controllo degli aggiornamenti del plugin Leaflet Elementor: ' . $response->get_error_message());
        return;
    }

    $release_data = json_decode(wp_remote_retrieve_body($response));
    $latest_version = $release_data->tag_name;

    $current_version = get_option('leaflet_elementor_widget_version');

    if ($force_update || version_compare($current_version, $latest_version, '<')) { 
        $download_url = $release_data->zipball_url;
        update_leaflet_elementor_widget($download_url, $latest_version);
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
        $plugin_dir = WP_PLUGIN_DIR . '/leaflet-elementor-widget';
        $extracted_folders = glob(WP_PLUGIN_DIR . '/Joolace-leaflet-elementor-*'); 
        if (!empty($extracted_folders)) {
            $extracted_folder_name = basename($extracted_folders[0]); 

            $old_path = WP_PLUGIN_DIR . '/' . $extracted_folder_name;
            $new_path = WP_PLUGIN_DIR . '/leaflet-elementor-widget';
            rename($old_path, $new_path);

            activate_plugin(plugin_basename(__FILE__));

            update_option('leaflet_elementor_widget_version', $latest_version);
            echo '<div class="notice notice-success is-dismissible"><p>Il plugin Leaflet Elementor Widget è stato aggiornato alla versione ' . $latest_version . '.</p></div>';
        } else {
            error_log('Errore durante l\'aggiornamento: la cartella del plugin non è stata trovata.');
            echo '<div class="notice notice-error"><p>Si è verificato un errore durante l\'aggiornamento del plugin: la cartella del plugin non è stata trovata.</p></div>';
        }
    }

    remove_action('admin_notices', 'leaflet_elementor_update_notice');
}



function activate_leaflet_elementor_widget() {
    update_option('leaflet_elementor_widget_version', '1.2.6');
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

    if (isset($_POST['save_settings'])) {
        update_option('enable_auto_update', isset($_POST['enable_auto_update']) ? 1 : 0);
        echo '<div class="notice notice-success is-dismissible"><p>Impostazioni salvate.</p></div>';
    }

    $current_version = get_option('leaflet_elementor_widget_version');
    $response = wp_remote_get('https://api.github.com/repos/Joolace/leaflet-elementor/releases/latest');
    $show_update_button = false; 

    if (!is_wp_error($response)) {
        $release_data = json_decode(wp_remote_retrieve_body($response));
        $latest_version = $release_data->tag_name;
        if (version_compare($current_version, $latest_version, '<')) {
            echo '<div class="notice notice-warning is-dismissible"><p>È disponibile una nuova versione del plugin Leaflet Elementor Widget (' . $latest_version . ').</p></div>';
            $show_update_button = true;
        }
    }

    echo '<div class="wrap">';
    echo '<h1>Impostazioni Leaflet Elementor Widget</h1>';

    // Inizio del form
    echo '<form method="post">'; 

    settings_fields('leaflet_elementor_widget_update_settings');
    do_settings_sections('leaflet-elementor-widget-settings');

    if ($show_update_button) {
        echo '<input type="submit" name="update_plugin" class="button button-primary" value="Aggiorna ora">';
    }

    echo '<input type="submit" name="save_settings" class="button button-primary" value="Salva modifiche">';
    echo '</form>';
    echo '</div>';
}

function leaflet_elementor_widget_update_settings_callback() {
    echo '<p>Controlla se vuoi abilitare l\'aggiornamento automatico del plugin.</p>';
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

register_activation_hook(__FILE__, 'activate_leaflet_elementor_widget');
add_action('admin_init', 'leaflet_elementor_widget_settings');
add_action('admin_menu', 'leaflet_elementor_widget_options_page');