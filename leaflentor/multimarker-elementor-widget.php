<?php
class MultiMarker_Elementor_Widget extends \Elementor\Widget_Base
{

    public function get_name()
    {
        return 'leaflentor-multimarker';
    }

    public function get_title()
    {
        return __('LeafLet MultiMarker', 'leaflentor');
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
        $this->map_id = 'multimarker-map-' . self::$map_count;
    }


    protected function register_controls()
    {
        $this->start_controls_section(
            'markers_section',
            [
                'label' => __('Markers', 'leaflentor'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'use_geojson',
            [
                'label' => __('Use GeoJSON', 'leaflentor'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'leaflentor'),
                'label_off' => __('No', 'leaflentor'),
                'return_value' => 'yes',
                'default' => 'no',
                'description' => __('NOTE: If "Use GeoJSON" is active the multimarkers will be disabled.', 'leaflentor'),
            ]
        );
        
        $this->add_control(
            'geojson_data',
            [
                'label' => __('GeoJSON Data', 'leaflentor'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => '',
                'condition' => [
                    'use_geojson' => 'yes',
                ],
            ]
        ); 
        
        $this->add_control(
            'marker_color',
            [
                'label' => __('Marker Color', 'leaflentor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#3388ff',
                'label_block' => true,
                'condition' => [
                    'use_geojson' => 'yes',
                ],
            ]
        );
        
        $this->add_control(
            'geojson_marker_color',
            [
                'label' => __('GeoJSON Marker Color', 'leaflentor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#3388ff',
                'condition' => [
                    'use_geojson' => 'yes',
                ],
            ]
        );
        
        $this->add_control(
            'geojson_polygon_color',
            [
                'label' => __('GeoJSON Polygon Color', 'leaflentor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#3388ff',
                'condition' => [
                    'use_geojson' => 'yes',
                ],
            ]
        );
        
        $this->add_control(
            'geojson_line_color',
            [
                'label' => __('GeoJSON Line Color', 'leaflentor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#3388ff',
                'condition' => [
                    'use_geojson' => 'yes',
                ],
            ]
        );   
        
        $this->add_control(
            'center_latitude',
            [
                'label' => __('Center Latitude', 'leaflentor'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => '',
                'label_block' => true,
                'description' => __('Enter the latitude of the center point for the map.', 'leaflentor'),
            ]
        );
    
        $this->add_control(
            'center_longitude',
            [
                'label' => __('Center Longitude', 'leaflentor'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => '',
                'label_block' => true,
                'description' => __('Enter the longitude of the center point for the map.', 'leaflentor'),
            ]
        );

        $repeater = new \Elementor\Repeater();

        $repeater->add_control(
            'latitude',
            [
                'label' => __('Latitude', 'leaflentor'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => '',
                'label_block' => true,
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $repeater->add_control(
            'longitude',
            [
                'label' => __('Longitude', 'leaflentor'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => '',
                'label_block' => true,
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $repeater->add_control(
            'popup_content',
            [
                'label' => __('Popup Content', 'leaflentor'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => '',
                'label_block' => true,
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $repeater->add_control(
            'marker_icon',
            [
                'label' => __('Marker Icon', 'leaflentor'),
                'type' => \Elementor\Controls_Manager::MEDIA,
                'default' => [
                    'url' => 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
                ],
                'label_block' => true,
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );
        

        $repeater->add_control(
            'marker_icon_size_horizontal',
            [
                'label' => __('Marker Icon Width', 'leaflentor'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 25,
                'min' => 1,
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'marker_icon_size_vertical',
            [
                'label' => __('Marker Icon Height', 'leaflentor'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 41,
                'min' => 1,
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'marker_icon_horizontal_anchor',
            [
                'label' => __('Marker Icon Horizontal Anchor', 'leaflentor'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 12,
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'marker_icon_vertical_anchor',
            [
                'label' => __('Marker Icon Vertical Anchor', 'leaflentor'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 41,
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'marker_popup_horizontal_anchor',
            [
                'label' => __('Marker Popup Horizontal Anchor', 'leaflentor'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 1,
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'marker_popup_vertical_anchor',
            [
                'label' => __('Marker Popup Vertical Anchor', 'leaflentor'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => -34,
                'label_block' => true,
            ]
        );

        $this->add_control(
            'markers',
            [
                'label' => __('Markers', 'leaflentor'),
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'title_field' => '{{{ latitude }}}, {{{ longitude }}}',
            ]
        );

        $this->add_control(
            'map_zoom',
            [
                'label' => __('Map Zoom', 'leaflentor'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 13,
                'min' => 1,
                'max' => 18,
            ]
        );

        $this->add_control(
            'min_zoom',
            [
                'label' => __('Min Zoom', 'leaflentor'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 3,
                'min' => 1,
                'max' => 18,
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'map_dimensions_section',
            [
                'label' => __('Map Dimensions', 'leaflentor'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );        

        $this->add_control(
            'desktop_height',
            [
                'label' => __('Desktop Height', 'leaflentor'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 500,
                'min' => 1,
                'label_block' => true,
            ]
        );

        $this->add_control(
            'tablet_height',
            [
                'label' => __('Tablet Height', 'leaflentor'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 400,
                'min' => 1,
                'label_block' => true,
            ]
        );

        $this->add_control(
            'mobile_height',
            [
                'label' => __('Mobile Height', 'leaflentor'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 300,
                'min' => 1,
                'label_block' => true,
            ]
        );

        $this->add_control(
            'desktop_width',
            [
                'label' => __('Desktop Width', 'leaflentor'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 100,
                'min' => 1,
                'label_block' => true,
            ]
        );

        $this->add_control(
            'tablet_width',
            [
                'label' => __('Tablet Width', 'leaflentor'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 80,
                'min' => 1,
                'label_block' => true,
            ]
        );

        $this->add_control(
            'mobile_width',
            [
                'label' => __('Mobile Width', 'leaflentor'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 60,
                'min' => 1,
                'label_block' => true,
            ]
        );

        $this->add_control(
            'desktop_height_unit',
            [
                'label' => __('Desktop Height Unit', 'leaflentor'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'px',
                'options' => [
                    'px' => __('px', 'leaflentor'),
                    '%' => __('%', 'leaflentor'),
                    'vh' => __('vh', 'leaflentor'),
                    'vw' => __('vw', 'leaflentor'),
                ],
            ]
        );

        $this->add_control(
            'tablet_height_unit',
            [
                'label' => __('Tablet Height Unit', 'leaflentor'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'px',
                'options' => [
                    'px' => __('px', 'leaflentor'),
                    '%' => __('%', 'leaflentor'),
                    'vh' => __('vh', 'leaflentor'),
                    'vw' => __('vw', 'leaflentor'),
                ],
            ]
        );

        $this->add_control(
            'mobile_height_unit',
            [
                'label' => __('Mobile Height Unit', 'leaflentor'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'px',
                'options' => [
                    'px' => __('px', 'leaflentor'),
                    '%' => __('%', 'leaflentor'),
                    'vh' => __('vh', 'leaflentor'),
                    'vw' => __('vw', 'leaflentor'),
                ],
            ]
        );

        $this->add_control(
            'desktop_width_unit',
            [
                'label' => __('Desktop Width Unit', 'leaflentor'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => '%',
                'options' => [
                    'px' => __('px', 'leaflentor'),
                    '%' => __('%', 'leaflentor'),
                    'vh' => __('vh', 'leaflentor'),
                    'vw' => __('vw', 'leaflentor'),
                ],
            ]
        );

        $this->add_control(
            'tablet_width_unit',
            [
                'label' => __('Tablet Width Unit', 'leaflentor'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => '%',
                'options' => [
                    'px' => __('px', 'leaflentor'),
                    '%' => __('%', 'leaflentor'),
                    'vh' => __('vh', 'leaflentor'),
                    'vw' => __('vw', 'leaflentor'),
                ],
            ]
        );

        $this->add_control(
            'mobile_width_unit',
            [
                'label' => __('Mobile Width Unit', 'leaflentor'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => '%',
                'options' => [
                    'px' => __('px', 'leaflentor'),
                    '%' => __('%', 'leaflentor'),
                    'vh' => __('vh', 'leaflentor'),
                    'vw' => __('vw', 'leaflentor'),
                ],
            ]
        );

        $this->add_control(
            'tiles_provider',
            [
                'label' => __('Tiles Provider', 'leaflentor'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'OpenStreetMap',
                'options' => [
                    'OpenStreetMap' => __('OpenStreetMap', 'leaflentor'),
                    'MtbMap' => __('MtbMap', 'leaflentor'),
                    'Esri Topo Map' => __('Esri Topo Map', 'leaflentor'),
                    'Esri Street Map' => __('Esri Street Map', 'leaflentor'),
                    'Esri World Imagery' => __('Esri World Imagery', 'leaflentor'),
                    'Carto DB Voyager' => __('Carto DB Voyager', 'leaflentor'),
                    'Carto DB Dark Matter' => __('Carto DB Dark Matter', 'leaflentor'),
                    'Carto DB Positron' => __('Carto DB Positron', 'leaflentor'),
                    'OPNVKarte' => __('OPNVKarte', 'leaflentor'),
                ],
            ]
        );
        
        $this->add_control(
            'use_custom_tiles',
            [
                'label' => __('Use Custom Tiles', 'leaflentor'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'leaflentor'),
                'label_off' => __('No', 'leaflentor'),
                'return_value' => 'yes',
                'default' => '',
            ]
        );
    
        $this->add_control(
            'custom_tiles_url',
            [
                'label' => __('Custom Tiles URL', 'leaflentor'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'condition' => [
                    'use_custom_tiles' => 'yes',
                ],
                'default' => '',
                'placeholder' => __('Enter tile URL...', 'leaflentor'),
            ]
        );
    
        $this->add_control(
            'custom_tiles_token',
            [
                'label' => __('Custom Tiles Token (if needed)', 'leaflentor'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'condition' => [
                    'use_custom_tiles' => 'yes',
                ],
                'default' => '',
                'placeholder' => __('Enter token...', 'leaflentor'),
            ]
        );
    
        $this->add_control(
            'custom_tiles_extension',
            [
                'label' => __('Custom Tiles Extension', 'leaflentor'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'condition' => [
                    'use_custom_tiles' => 'yes',
                ],
                'default' => 'png',
                'placeholder' => __('Enter file extension (e.g., png, jpg)...', 'leaflentor'),
            ]
        );

        $this->end_controls_section();

    }
    protected function render() {
        $settings = $this->get_settings_for_display();
        $markers = $settings['markers'];
        $desktop_height = $settings['desktop_height'];
        $tablet_height = $settings['tablet_height'];
        $mobile_height = $settings['mobile_height'];
        $desktop_width = $settings['desktop_width'];
        $tablet_width = $settings['tablet_width'];
        $mobile_width = $settings['mobile_width'];
        $desktop_height_unit = $settings['desktop_height_unit'];
        $tablet_height_unit = $settings['tablet_height_unit'];
        $mobile_height_unit = $settings['mobile_height_unit'];
        $desktop_width_unit = $settings['desktop_width_unit'];
        $tablet_width_unit = $settings['tablet_width_unit'];
        $mobile_width_unit = $settings['mobile_width_unit'];
        $tiles_provider = $settings['tiles_provider'];
        $map_zoom = $settings['map_zoom'];
        $use_geojson = $settings['use_geojson'];
        $geojson_data = $settings['geojson_data'];
        $geojson_marker_color = $settings['geojson_marker_color'];
        $geojson_polygon_color = $settings['geojson_polygon_color'];
        $geojson_line_color = $settings['geojson_line_color'];
        $center_latitude = $settings['center_latitude'];
        $center_longitude = $settings['center_longitude'];
        $use_custom_tiles = $settings['use_custom_tiles'];
        $custom_tiles_token = $settings['custom_tiles_token'];
        $custom_tiles_extension = $settings['custom_tiles_extension'];
        $custom_tiles_url = $settings['custom_tiles_url'];
        $min_zoom = $settings['min_zoom'];

        if ($use_custom_tiles === 'yes' && !empty($custom_tiles_url)) {
            $tiles_url = str_replace(['{accessToken}', '{ext}'], [$custom_tiles_token, $custom_tiles_extension], $custom_tiles_url);
        } else {
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
        }

$start_lat = is_numeric($center_latitude) ? (float) $center_latitude : ( (isset($markers[0]['latitude']) && is_numeric($markers[0]['latitude'])) ? (float) $markers[0]['latitude'] : 0 );
$start_lng = is_numeric($center_longitude) ? (float) $center_longitude : ( (isset($markers[0]['longitude']) && is_numeric($markers[0]['longitude'])) ? (float) $markers[0]['longitude'] : 0 );

$map_id_json  = wp_json_encode( $this->map_id );
$tiles_json   = wp_json_encode( $tiles_url );
$zoom_json    = wp_json_encode( (int) $map_zoom );
$minzoom_json = wp_json_encode( (int) $min_zoom );

$geojson_arr = json_decode( (string) $geojson_data, true );
$geojson_safe = (json_last_error() === JSON_ERROR_NONE) ? wp_json_encode( $geojson_arr ) : 'null';

// Colori
$gj_poly_color  = wp_json_encode( $geojson_polygon_color );
$gj_line_color  = wp_json_encode( $geojson_line_color );
$gj_mark_color  = wp_json_encode( $geojson_marker_color );

$markers_safe = [];
if (is_array($markers)) {
    foreach ($markers as $m) {
        $markers_safe[] = [
            'lat'   => isset($m['latitude'])  && is_numeric($m['latitude'])  ? (float) $m['latitude']  : null,
            'lng'   => isset($m['longitude']) && is_numeric($m['longitude']) ? (float) $m['longitude'] : null,
            'popup' => isset($m['popup_content']) ? wp_kses_post($m['popup_content']) : '',
            'icon'  => isset($m['marker_icon']['url']) ? esc_url_raw($m['marker_icon']['url']) : '',
            'iw'    => isset($m['marker_icon_size_horizontal']) ? (int) $m['marker_icon_size_horizontal'] : 25,
            'ih'    => isset($m['marker_icon_size_vertical'])   ? (int) $m['marker_icon_size_vertical']   : 41,
            'iax'   => isset($m['marker_icon_horizontal_anchor']) ? (int) $m['marker_icon_horizontal_anchor'] : 12,
            'iay'   => isset($m['marker_icon_vertical_anchor'])   ? (int) $m['marker_icon_vertical_anchor']   : 41,
            'pax'   => isset($m['marker_popup_horizontal_anchor']) ? (int) $m['marker_popup_horizontal_anchor'] : 1,
            'pay'   => isset($m['marker_popup_vertical_anchor'])   ? (int) $m['marker_popup_vertical_anchor']   : -34,
            'hue'   => isset($m['marker_color']) ? $m['marker_color'] : '', // lasciamo string, lo encodiamo dopo
        ];
    }
}
$markers_json = wp_json_encode( $markers_safe );
$start_lat_json = wp_json_encode( $start_lat );
$start_lng_json = wp_json_encode( $start_lng );


        if (!empty($markers) || ($use_geojson === 'yes' && !empty($geojson_data))) {
            echo '<style>
@media screen and (max-width: 768px) {
    #' . esc_attr($this->map_id) . ' {
        height: ' . esc_attr($mobile_height) . esc_attr($mobile_height_unit) . ';
        width: '  . esc_attr($mobile_width)  . esc_attr($mobile_width_unit)  . ';
    }
}
@media screen and (min-width: 769px) and (max-width: 1024px) {
    #' . esc_attr($this->map_id) . ' {
        height: ' . esc_attr($tablet_height) . esc_attr($tablet_height_unit) . ';
        width: '  . esc_attr($tablet_width)  . esc_attr($tablet_width_unit)  . ';
    }
}
@media screen and (min-width: 1025px) {
    #' . esc_attr($this->map_id) . ' {
        height: ' . esc_attr($desktop_height) . esc_attr($desktop_height_unit) . ';
        width: '  . esc_attr($desktop_width)  . esc_attr($desktop_width_unit)  . ';
    }
}
</style>';
            echo '<div id="' . esc_attr($this->map_id) . '" class="leaflet-map"></div>';
           echo '<script>
jQuery(function($){
    var map = L.map(' . wp_json_encode( $this->map_id ) . ').setView([' . wp_json_encode( (float) $start_lat ) . ', ' . wp_json_encode( (float) $start_lng ) . '], ' . wp_json_encode( (int) $map_zoom ) . ');

    L.tileLayer(' . wp_json_encode( $tiles_url ) . ', {
        attribution: "© OpenStreetMap contributors"
    }).addTo(map);

    L.control.fullscreen({ position: "topleft" }).addTo(map);

    map.on("zoomend", function(){
        if (map.getZoom() < ' . wp_json_encode( (int) $min_zoom ) . ') {
            map.setZoom(' . wp_json_encode( (int) $min_zoom ) . ');
        }
    });

    var useGeoJSON = ' . wp_json_encode( $use_geojson === "yes" ) . ';
    var geojsonData = ' . ( ( json_last_error() === JSON_ERROR_NONE ) ? wp_json_encode( $geojson_arr ) : 'null' ) . ';

    if (useGeoJSON && geojsonData) {
        var gj = L.geoJSON(geojsonData, {
            style: function(feature){
                switch (feature.geometry.type) {
                    case "Polygon":
                    case "MultiPolygon":
                        return { color: ' . wp_json_encode( $geojson_polygon_color ) . ' };
                    case "LineString":
                    case "MultiLineString":
                        return { color: ' . wp_json_encode( $geojson_line_color ) . ' };
                    default:
                        return {};
                }
            },
            pointToLayer: function (feature, latlng) {
                return L.circleMarker(latlng, {
                    radius: 8,
                    fillColor: ' . wp_json_encode( $geojson_marker_color ) . ',
                    color: "#000",
                    weight: 1,
                    opacity: 1,
                    fillOpacity: 0.8
                });
            }
        }).addTo(map);
    } else {
        var markers = ' . wp_json_encode( $markers_safe ) . ';
        if (Array.isArray(markers)) {
            markers.forEach(function(m){
                if (typeof m.lat === "number" && typeof m.lng === "number") {
                    var icon = L.icon({
                        iconUrl: m.icon,
                        iconSize: [m.iw, m.ih],
                        iconAnchor: [m.iax, m.iay],
                        popupAnchor: [m.pax, m.pay]
                    });
                    var mk = L.marker([m.lat, m.lng], { icon: icon }).addTo(map)
                        .bindPopup(m.popup, { autoClose: false });

                    if (m.hue && mk._icon) {
                        mk._icon.style.filter = "hue-rotate(" + m.hue + ")";
                    }
                }
            });
        }
    }
});
</script>';
        } else {
            echo '<p>' . esc_html__( 'No markers added.', 'leaflentor' ) . '</p>';
        }
    }
}
