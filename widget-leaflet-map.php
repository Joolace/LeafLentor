<?php

namespace LeafLetRoutingPlugin;

require_once(ABSPATH . 'wp-admin/includes/plugin.php');

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;

class Leaflet_Map_Widget extends Widget_Base
{

    public function get_name()
    {
        return 'leaflet-map-widget';
    }

    public function get_title()
    {
        return __('Leaflet Routing Machine', 'leaflet-map-widget');
    }

    public function get_icon()
    {
        return 'eicon-map-pin';
    }

    public function get_categories()
    {
        return ['general'];
    }

    private static $map_count = 0;


    private $map_id;

    public function __construct($data = [], $args = null)
    {
        parent::__construct($data, $args);
        self::$map_count++;
        $this->map_id = 'map-routing-' . self::$map_count;
    }

    protected function _register_controls()
    {
        $this->start_controls_section(
            'marker_section',
            [
                'label' => __('Marker', 'leaflet-map-widget'),
            ]
        );
    
        $marker_repeater = new \Elementor\Repeater();
    
        $marker_repeater->add_control(
            'marker_latitude',
            [
                'label' => __('Latitude', 'leaflet-map-widget'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => '51.5',
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );
    
        $marker_repeater->add_control(
            'marker_longitude',
            [
                'label' => __('Longitude', 'leaflet-map-widget'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => '-0.09',
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );
    
        $marker_repeater->add_control(
            'waypoints',
            [
                'label' => __('Waypoints', 'leaflet-map-widget'),
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => [
                    [
                        'name' => 'waypoint_latitude',
                        'label' => __('Waypoint Latitude', 'leaflet-map-widget'),
                        'type' => \Elementor\Controls_Manager::TEXT,
                        'default' => '',
                        'dynamic' => [
                            'active' => true,
                        ],
                    ],
                    [
                        'name' => 'waypoint_longitude',
                        'label' => __('Waypoint Longitude', 'leaflet-map-widget'),
                        'type' => \Elementor\Controls_Manager::TEXT,
                        'default' => '',
                        'dynamic' => [
                            'active' => true,
                        ],
                    ],
                ],
                'title_field' => '{{{ waypoint_latitude }}}',
            ]
        );
    
        $this->add_control(
            'markers',
            [
                'label' => __('Markers', 'leaflet-map-widget'),
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => $marker_repeater->get_controls(),
                'default' => [],
                'title_field' => __('Marker', 'leaflet-map-widget'),
            ]
        );
    
        $this->end_controls_section();
    
        $this->start_controls_section(
            'map_settings_section',
            [
                'label' => __('Map Settings', 'leaflet-map-widget'),
            ]
        );
    
        $this->add_control(
            'map_width',
            [
                'label' => __('Width', 'leaflet-map-widget'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 400,
                'description' => __('Enter map width.', 'leaflet-map-widget'),
            ]
        );
    
        $this->add_control(
            'map_width_unit',
            [
                'label' => __('Unit', 'leaflet-map-widget'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'px',
                'options' => [
                    'px' => 'px',
                    '%' => '%',
                    'vh' => 'vh',
                    'vw' => 'vw',
                ],
            ]
        );
    
        $this->add_control(
            'map_height',
            [
                'label' => __('Height', 'leaflet-map-widget'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 500,
                'description' => __('Enter map height.', 'leaflet-map-widget'),
            ]
        );

        $this->add_control(
            'map_height_laptop',
            [
                'label' => __('Height (Laptop)', 'leaflet-map-widget'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 400,
                'min' => 100,
                'max' => 1000,
                'step' => 10,
            ]
        );

        $this->add_control(
            'map_height_tablet_horizontal',
            [
                'label' => __('Height (Horizontal Tablet)', 'leaflet-map-widget'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 350,
                'min' => 100,
                'max' => 1000,
                'step' => 10,
            ]
        );

        $this->add_control(
            'map_height_tablet',
            [
                'label' => __('Height (Tablet)', 'leaflet-map-widget'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 350,
                'min' => 100,
                'max' => 800,
                'step' => 10,
            ]
        );

        $this->add_control(
            'map_height_mobile',
            [
                'label' => __('Height (Mobile)', 'leaflet-map-widget'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 300,
                'min' => 100,
                'max' => 800,
                'step' => 10,
            ]
        );
    
        $this->add_control(
            'map_height_unit',
            [
                'label' => __('Unit', 'leaflet-map-widget'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'px',
                'options' => [
                    'px' => 'px',
                    '%' => '%',
                    'vh' => 'vh',
                    'vw' => 'vw',
                ],
            ]
        );
    
        $this->add_control(
            'mapbox_api_key',
            [
                'label' => __('Mapbox API Key', 'leaflet-map-widget'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'description' => __('Enter your Mapbox API Key.', 'leaflet-map-widget'),
            ]
        );
    
        $this->end_controls_section();
    
        $this->start_controls_section(
            'line_style_section',
            [
                'label' => __('Line Style', 'leaflet-map-widget'),
            ]
        );
    
        $this->add_control(
            'line_color',
            [
                'label' => __('Line Color', 'leaflet-map-widget'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#3388ff',
                'selectors' => [
                    '{{WRAPPER}} .leaflet-routing-container .leaflet-routing-line' => 'color: {{VALUE}};',
                ],
            ]
        );
    
        $this->add_control(
            'line_opacity',
            [
                'label' => __('Line Opacity', 'leaflet-map-widget'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'default' => [
                    'size' => 0.6,
                ],
                'range' => [
                    'px' => [
                        'max' => 1,
                        'min' => 0.1,
                        'step' => 0.1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .leaflet-routing-container .leaflet-routing-line' => 'opacity: {{SIZE}};',
                ],
            ]
        );
    
        $this->add_control(
            'line_weight',
            [
                'label' => __('Line Weight', 'leaflet-map-widget'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'default' => [
                    'size' => 5,
                ],
                'range' => [
                    'px' => [
                        'max' => 10,
                        'min' => 1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .leaflet-routing-container .leaflet-routing-line' => 'weight: {{SIZE}};',
                ],
            ]
        );
    
        $this->end_controls_section();
    
        $this->start_controls_section(
            'routing_settings_section',
            [
                'label' => __('Routing Settings', 'leaflet-map-widget'),
            ]
        );
    
        $this->add_control(
            'routing_type',
            [
                'label' => __('Routing Type', 'leaflet-map-widget'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'road',
                'options' => [
                    'road' => __('Road', 'leaflet-map-widget'),
                    'pedestrian' => __('Pedestrian', 'leaflet-map-widget'),
                ],
            ]
        );
    
        $this->add_control(
            'tiles_provider',
            [
                'label' => __('Tiles Provider', 'leaflet-map-widget'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'OpenStreetMap' => 'OpenStreetMap',
                    'MtbMap' => 'MtbMap',
                    'Esri Topo Map' => 'Esri Topo Map',
                    'Esri Street Map' => 'Esri Street Map',
                    'Esri World Imagery' => 'Esri World Imagery',
                    'Carto DB Voyager' => 'Carto DB Voyager',
                    'Carto DB Dark Matter' => 'Carto DB Dark Matter',
                    'Carto DB Positron' => 'Carto DB Positron',
                    'OPNVKarte' => 'OPNVKarte',
                ],
                'default' => 'OpenStreetMap',
            ]
        );
    
        $this->end_controls_section();
    }    

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $markers = isset($settings['markers']) ? $settings['markers'] : [];
        $map_width = isset($settings['map_width']) ? $settings['map_width'] : 400;
        $map_width_unit = isset($settings['map_width_unit']) ? $settings['map_width_unit'] : 'px';
        $map_height = isset($settings['map_height']) ? $settings['map_height'] : 500;
        $map_height_mobile = isset($settings['map_height_mobile']) ? $settings['map_height_mobile'] : 300;
        $map_height_tablet = isset($settings['map_height_tablet']) ? $settings['map_height_tablet'] : 350;
        $map_height_tablet_horizontal = isset($settings['map_height_tablet_horizontal']) ? $settings['map_height_tablet_horizontal'] : 350;
        $map_height_laptop = isset($settings['map_height_laptop']) ? $settings['map_height_laptop'] : 400;
        $map_height_unit = isset($settings['map_height_unit']) ? $settings['map_height_unit'] : 'px';
        $settings = $this->get_settings_for_display();
        $line_color = isset($settings['line_color']) ? $settings['line_color'] : '#3388ff';
        $line_opacity = isset($settings['line_opacity']['size']) ? $settings['line_opacity']['size'] : 0.6;
        $line_weight = isset($settings['line_weight']['size']) ? $settings['line_weight']['size'] : 5;
        $tiles_provider = isset($settings['tiles_provider']) ? $settings['tiles_provider'] : 'OpenStreetMap';

        switch ($tiles_provider) {
            case 'Esri World Imagery':
                $tiles_url = 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}';
                break;
            case 'Esri Topo Map':
                $tiles_url = 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Topo_Map/MapServer/tile/{z}/{y}/{x}';
                break;
            case 'Esri Street Map':
                $tiles_url = 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Street_Map/MapServer/tile/{z}/{y}/{x}';
                break;
            case 'MtbMap':
                $tiles_url = 'http://tile.mtbmap.cz/mtbmap_tiles/{z}/{x}/{y}.png';
                break;
            case 'Carto DB Voyager':
                $tiles_url = 'https://{s}.basemaps.cartocdn.com/rastertiles/voyager_labels_under/{z}/{x}/{y}{r}.png';
                break;
            case 'Carto DB Dark Matter':
                $tiles_url = 'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png';
                break;
            case 'Carto DB Positron':
                $tiles_url = 'https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png';
                break;
            case 'OPNVKarte':
                $tiles_url = 'https://tileserver.memomaps.de/tilegen/{z}/{x}/{y}.png';
                break;
            case 'OpenStreetMap':
            default:
                $tiles_url = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
                break;
        }
?>
<style>
    @media (max-width: 767px) {
        #<?php echo esc_attr($this->map_id); ?> {
            height: <?php echo esc_attr($map_height_mobile) . esc_attr($map_height_unit); ?> !important;
        }
    }

    @media (min-width: 768px) and (max-width: 1024px) {
        #<?php echo esc_attr($this->map_id); ?> {
            height: <?php echo esc_attr($map_height_tablet) . esc_attr($map_height_unit); ?> !important;
        }
    }

    @media (min-width: 1024px) and (max-width: 1200px) {
        #<?php echo esc_attr($this->map_id); ?> {
            height: <?php echo esc_attr($map_height_tablet_horizontal) . esc_attr($map_height_unit); ?> !important; 
        }
    }

    @media (min-width: 1200px) and (max-width: 1366px) {
        #<?php echo esc_attr($this->map_id); ?> {
            height: <?php echo esc_attr($map_height_laptop) . esc_attr($map_height_unit); ?> !important; 
        }
    }
</style>

<div id="<?php echo esc_attr($this->map_id); ?>" style="width: <?php echo esc_attr($map_width) . esc_attr($map_width_unit); ?>; height: <?php echo esc_attr($map_height) . esc_attr($map_height_unit); ?>;"></div>
<script>
    jQuery(document).ready(function($) {
        var map = L.map('<?php echo esc_js($this->map_id); ?>').setView([51.505, -0.09], 13);
        var waypoints = [];

        L.tileLayer(<?php echo json_encode($tiles_url); ?>, {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        L.control.fullscreen({
            position: 'topleft'
        }).addTo(map);

        var markersData = <?php echo json_encode($markers); ?>;
        var mapboxApiKey = '<?php echo esc_js($settings['mapbox_api_key']); ?>';
        var routingType = '<?php echo esc_js($settings['routing_type']); ?>';

        if (routingType === 'road') {
            var routerProfile = 'mapbox/driving';
        } else if (routingType === 'pedestrian') {
            var routerProfile = 'mapbox/walking';
        }

        markersData.forEach(function(markerData) {
            if (markerData.marker_latitude && markerData.marker_longitude) {
                var marker = L.marker([markerData.marker_latitude, markerData.marker_longitude]).addTo(map);
                var popupContent = '';
                marker.bindPopup(popupContent);
                waypoints.push(marker.getLatLng());

                if (markerData.waypoints && Array.isArray(markerData.waypoints)) {
                    markerData.waypoints.forEach(function(waypoint) {
                        if (waypoint.waypoint_latitude && waypoint.waypoint_longitude) {
                            waypoints.push(L.latLng(waypoint.waypoint_latitude, waypoint.waypoint_longitude));
                        }
                    });
                }
            }
        });

        if (waypoints.length > 1) {
            var control = L.Routing.control({
                waypoints: waypoints,
                routeWhileDragging: false,
                show: false,
                router: L.Routing.mapbox(mapboxApiKey, {
                    profile: routerProfile,
                }),
                collapsible: false,
                createMarker: function(i, wp, nWps) {
                    if (!<?php echo is_user_logged_in() ? 'true' : 'false'; ?>) {
                        return null;
                    }
                    return L.marker(wp.latLng, {
                        draggable: false
                    });
                },
                lineOptions: {
                    styles: [{
                        color: '<?php echo esc_attr($line_color); ?>',
                        opacity: <?php echo esc_attr($line_opacity); ?>,
                        weight: <?php echo esc_attr($line_weight); ?>
                    }]
                },
                language: 'it'
            }).addTo(map);
        }
    });
</script>

<?php
    }


    protected function _content_template()
    {
    }
}
