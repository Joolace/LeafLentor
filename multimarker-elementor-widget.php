<?php
class MultiMarker_Elementor_Widget extends \Elementor\Widget_Base
{

    public function get_name()
    {
        return 'multi-marker-elementor-widget';
    }

    public function get_title()
    {
        return __('LeafLet MultiMarker', 'multi-marker-elementor-widget');
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


    protected function _register_controls()
    {
        $this->start_controls_section(
            'markers_section',
            [
                'label' => __('Markers', 'multi-marker-elementor-widget'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $repeater = new \Elementor\Repeater();

        $repeater->add_control(
            'latitude',
            [
                'label' => __('Latitude', 'multi-marker-elementor-widget'),
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
                'label' => __('Longitude', 'multi-marker-elementor-widget'),
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
                'label' => __('Popup Content', 'multi-marker-elementor-widget'),
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
                'label' => __('Marker Icon', 'multi-marker-elementor-widget'),
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
                'label' => __('Marker Icon Width', 'multi-marker-elementor-widget'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 25,
                'min' => 1,
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'marker_icon_size_vertical',
            [
                'label' => __('Marker Icon Height', 'multi-marker-elementor-widget'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 41,
                'min' => 1,
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'marker_icon_horizontal_anchor',
            [
                'label' => __('Marker Icon Horizontal Anchor', 'multi-marker-elementor-widget'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 12,
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'marker_icon_vertical_anchor',
            [
                'label' => __('Marker Icon Vertical Anchor', 'multi-marker-elementor-widget'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 41,
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'marker_popup_horizontal_anchor',
            [
                'label' => __('Marker Popup Horizontal Anchor', 'multi-marker-elementor-widget'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 1,
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'marker_popup_vertical_anchor',
            [
                'label' => __('Marker Popup Vertical Anchor', 'multi-marker-elementor-widget'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => -34,
                'label_block' => true,
            ]
        );

        $this->add_control(
            'markers',
            [
                'label' => __('Markers', 'multi-marker-elementor-widget'),
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'title_field' => '{{{ latitude }}}, {{{ longitude }}}',
            ]
        );

        $this->add_control(
            'map_zoom',
            [
                'label' => __('Map Zoom', 'multi-marker-elementor-widget'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 13,
                'min' => 1,
                'max' => 18,
                'label_block' => true,
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'map_dimensions_section',
            [
                'label' => __('Map Dimensions', 'multi-marker-elementor-widget'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );        

        $this->add_control(
            'desktop_height',
            [
                'label' => __('Desktop Height', 'multi-marker-elementor-widget'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 500,
                'min' => 1,
                'label_block' => true,
            ]
        );

        $this->add_control(
            'tablet_height',
            [
                'label' => __('Tablet Height', 'multi-marker-elementor-widget'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 400,
                'min' => 1,
                'label_block' => true,
            ]
        );

        $this->add_control(
            'mobile_height',
            [
                'label' => __('Mobile Height', 'multi-marker-elementor-widget'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 300,
                'min' => 1,
                'label_block' => true,
            ]
        );

        $this->add_control(
            'desktop_width',
            [
                'label' => __('Desktop Width', 'multi-marker-elementor-widget'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 100,
                'min' => 1,
                'label_block' => true,
            ]
        );

        $this->add_control(
            'tablet_width',
            [
                'label' => __('Tablet Width', 'multi-marker-elementor-widget'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 80,
                'min' => 1,
                'label_block' => true,
            ]
        );

        $this->add_control(
            'mobile_width',
            [
                'label' => __('Mobile Width', 'multi-marker-elementor-widget'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 60,
                'min' => 1,
                'label_block' => true,
            ]
        );

        $this->add_control(
            'desktop_height_unit',
            [
                'label' => __('Desktop Height Unit', 'multi-marker-elementor-widget'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'px',
                'options' => [
                    'px' => __('px', 'multi-marker-elementor-widget'),
                    '%' => __('%', 'multi-marker-elementor-widget'),
                    'vh' => __('vh', 'multi-marker-elementor-widget'),
                    'vw' => __('vw', 'multi-marker-elementor-widget'),
                ],
            ]
        );

        $this->add_control(
            'tablet_height_unit',
            [
                'label' => __('Tablet Height Unit', 'multi-marker-elementor-widget'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'px',
                'options' => [
                    'px' => __('px', 'multi-marker-elementor-widget'),
                    '%' => __('%', 'multi-marker-elementor-widget'),
                    'vh' => __('vh', 'multi-marker-elementor-widget'),
                    'vw' => __('vw', 'multi-marker-elementor-widget'),
                ],
            ]
        );

        $this->add_control(
            'mobile_height_unit',
            [
                'label' => __('Mobile Height Unit', 'multi-marker-elementor-widget'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'px',
                'options' => [
                    'px' => __('px', 'multi-marker-elementor-widget'),
                    '%' => __('%', 'multi-marker-elementor-widget'),
                    'vh' => __('vh', 'multi-marker-elementor-widget'),
                    'vw' => __('vw', 'multi-marker-elementor-widget'),
                ],
            ]
        );

        $this->add_control(
            'desktop_width_unit',
            [
                'label' => __('Desktop Width Unit', 'multi-marker-elementor-widget'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => '%',
                'options' => [
                    'px' => __('px', 'multi-marker-elementor-widget'),
                    '%' => __('%', 'multi-marker-elementor-widget'),
                    'vh' => __('vh', 'multi-marker-elementor-widget'),
                    'vw' => __('vw', 'multi-marker-elementor-widget'),
                ],
            ]
        );

        $this->add_control(
            'tablet_width_unit',
            [
                'label' => __('Tablet Width Unit', 'multi-marker-elementor-widget'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => '%',
                'options' => [
                    'px' => __('px', 'multi-marker-elementor-widget'),
                    '%' => __('%', 'multi-marker-elementor-widget'),
                    'vh' => __('vh', 'multi-marker-elementor-widget'),
                    'vw' => __('vw', 'multi-marker-elementor-widget'),
                ],
            ]
        );

        $this->add_control(
            'mobile_width_unit',
            [
                'label' => __('Mobile Width Unit', 'multi-marker-elementor-widget'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => '%',
                'options' => [
                    'px' => __('px', 'multi-marker-elementor-widget'),
                    '%' => __('%', 'multi-marker-elementor-widget'),
                    'vh' => __('vh', 'multi-marker-elementor-widget'),
                    'vw' => __('vw', 'multi-marker-elementor-widget'),
                ],
            ]
        );

        $this->add_control(
            'tiles_provider',
            [
                'label' => __('Tiles Provider', 'multi-marker-elementor-widget'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'OpenStreetMap',
                'options' => [
                    'OpenStreetMap' => __('OpenStreetMap', 'multi-marker-elementor-widget'),
                    'MtbMap' => __('MtbMap', 'multi-marker-elementor-widget'),
                    'Esri Topo Map' => __('Esri Topo Map', 'multi-marker-elementor-widget'),
                    'Esri Street Map' => __('Esri Street Map', 'multi-marker-elementor-widget'),
                    'Esri World Imagery' => __('Esri World Imagery', 'multi-marker-elementor-widget'),
                    'Carto DB Voyager' => __('Carto DB Voyager', 'multi-marker-elementor-widget'),
                    'Carto DB Dark Matter' => __('Carto DB Dark Matter', 'multi-marker-elementor-widget'),
                    'Carto DB Positron' => __('Carto DB Positron', 'multi-marker-elementor-widget'),
                    'OPNVKarte' => __('OPNVKarte', 'multi-marker-elementor-widget'),
                ],
            ]
        );

        $this->end_controls_section();

    }

    protected function render()
    {
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
        $media_query = "
            @media screen and (max-width: 768px) {
                #{$this->map_id} {
                    height: {$mobile_height}{$mobile_height_unit};
                    width: {$mobile_width}{$mobile_width_unit};
                }
            }
            @media screen and (min-width: 769px) and (max-width: 1024px) {
                #{$this->map_id} {
                    height: {$tablet_height}{$tablet_height_unit};
                    width: {$tablet_width}{$tablet_width_unit};
                }
            }
            @media screen and (min-width: 1025px) {
                #{$this->map_id} {
                    height: {$desktop_height}{$desktop_height_unit};
                    width: {$desktop_width}{$desktop_width_unit};
                }
            }
        ";

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

        if (!empty($markers)) {
            echo '<style>' . $media_query . '</style>';
            echo '<div id="' . $this->map_id . '" class="leaflet-map"></div>';
            echo '<script>
            jQuery(document).ready(function($) {
                var map = L.map("' . $this->map_id . '").setView([' . $markers[0]['latitude'] . ', ' . $markers[0]['longitude'] . '], ' . $map_zoom . ');
                L.tileLayer("' . $tiles_url . '", {
                    attribution: "© OpenStreetMap contributors"
                }).addTo(map);';
                echo 'L.control.fullscreen({
                    position: "topleft"
                }).addTo(map);';
                

            foreach ($markers as $marker) {
                $latitude = $marker['latitude'];
                $longitude = $marker['longitude'];
                $popup_content = $marker['popup_content'];
                $icon_url = $marker['marker_icon']['url'];
                $icon_size_horizontal = $marker['marker_icon_size_horizontal'];
                $icon_size_vertical = $marker['marker_icon_size_vertical'];
                $icon_horizontal_anchor = $marker['marker_icon_horizontal_anchor'];
                $icon_vertical_anchor = $marker['marker_icon_vertical_anchor'];
                $popup_horizontal_anchor = $marker['marker_popup_horizontal_anchor'];
                $popup_vertical_anchor = $marker['marker_popup_vertical_anchor'];

                if (is_numeric($latitude) && is_numeric($longitude)) {
                    echo 'var customIcon = L.icon({
                        iconUrl: "' . $icon_url . '",
                        iconSize: [' . $icon_size_horizontal . ', ' . $icon_size_vertical . '],
                        iconAnchor: [' . $icon_horizontal_anchor . ', ' . $icon_vertical_anchor . '],
                        popupAnchor: [' . $popup_horizontal_anchor . ', ' . $popup_vertical_anchor . ']
                    });

                    L.marker([' . $latitude . ', ' . $longitude . '], { icon: customIcon }).addTo(map)
                        .bindPopup("' . addslashes($popup_content) . '");
                    ';
                }
            }

            echo '});
            </script>';
        } else {
            echo '<p>No markers added.</p>';
        }
    }
}
