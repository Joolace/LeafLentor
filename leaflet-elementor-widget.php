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