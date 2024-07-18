<?php
class Leaflet_Elementor_Widget extends \Elementor\Widget_Base
{

    public function get_name()
    {
        return 'leaflet-elementor-widget';
    }

    public function get_title()
    {
        return __('Leaflet Widget', 'leaflet-elementor-widget');
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
        $this->map_id = 'map-' . self::$map_count;
    }


    protected function _register_controls()
    {
        $this->start_controls_section(
            'coordinates_section',
            [
                'label' => __('Coordinates', 'leaflet-elementor-widget'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'latitude',
            [
                'label' => __('Latitude', 'leaflet-elementor-widget'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => '',
                'placeholder' => __('Enter latitude...', 'leaflet-elementor-widget'),
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $this->add_control(
            'longitude',
            [
                'label' => __('Longitude', 'leaflet-elementor-widget'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => '',
                'placeholder' => __('Enter longitude...', 'leaflet-elementor-widget'),
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'popup_content_section',
            [
                'label' => __('Popup Content', 'leaflet-elementor-widget'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'popup_content',
            [
                'label' => __('Popup Content', 'leaflet-elementor-widget'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => '',
                'placeholder' => __('Enter popup content...', 'leaflet-elementor-widget'),
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'other_settings_section',
            [
                'label' => __('Other Settings', 'leaflet-elementor-widget'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'enable_fullscreen',
            [
                'label' => __('Enable Fullscreen', 'leaflet-elementor-widget'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
                'label_on' => __('Yes', 'leaflet-elementor-widget'),
                'label_off' => __('No', 'leaflet-elementor-widget'),
                'return_value' => 'yes',
            ]
        );

        $this->add_control(
            'height',
            [
                'label' => __('Height (Desktop)', 'leaflet-elementor-widget'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 500,
                'min' => 100,
                'max' => 1000,
                'step' => 10,
            ]
        );

        $this->add_control(
            'height_laptop',
            [
                'label' => __('Height (Laptop)', 'leaflet-elementor-widget'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 400,
                'min' => 100,
                'max' => 1000,
                'step' => 10,
            ]
        );

        $this->add_control(
            'height_tablet_horizontal',
            [
                'label' => __('Height (Horizontal Tablet)', 'leaflet-elementor-widget'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 350,
                'min' => 100,
                'max' => 1000,
                'step' => 10,
            ]
        );

        $this->add_control(
            'height_tablet',
            [
                'label' => __('Height (Tablet)', 'leaflet-elementor-widget'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 350,
                'min' => 100,
                'max' => 800,
                'step' => 10,
            ]
        );

        $this->add_control(
            'height_mobile',
            [
                'label' => __('Height (Mobile)', 'leaflet-elementor-widget'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 300,
                'min' => 100,
                'max' => 800,
                'step' => 10,
            ]
        );

        $this->add_control(
            'height_unit',
            [
                'label' => __('Height Unit', 'leaflet-elementor-widget'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'px' => 'px',
                    'vh' => 'vh',
                    'rem' => 'rem',
                    '%' => '%',
                ],
                'default' => 'px',
            ]
        );

        $this->add_control(
            'width',
            [
                'label' => __('Width', 'leaflet-elementor-widget'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 600,
                'min' => 100,
                'max' => 2000,
                'step' => 10,
            ]
        );

        $this->add_control(
            'width_unit',
            [
                'label' => __('Width Unit', 'leaflet-elementor-widget'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'px' => 'px',
                    'vh' => 'vh',
                    'rem' => 'rem',
                    '%' => '%',
                ],
                'default' => 'px',
            ]
        );

        $this->add_control(
            'tiles_provider',
            [
                'label' => __('Tiles Provider', 'leaflet-elementor-widget'),
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

        $this->add_control(
            'attribution_text',
            [
                'label' => __('Attribution Text', 'leaflet-elementor-widget'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => '© <a href=\"https://www.openstreetmap.org/copyright\">OpenStreetMap</a> contributors',
                'placeholder' => __('Enter attribution text...', 'leaflet-elementor-widget'),
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'border_style_section',
            [
                'label' => __('Stile del Bordo', 'leaflet-elementor-widget'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'border_radius',
            [
                'label' => __('Border Radius', 'leaflet-elementor-widget'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', '%', 'em', 'rem'],
                'selectors' => [
                    '{{WRAPPER}} .leaflet-container' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'border_width',
            [
                'label' => __('Border Width', 'leaflet-elementor-widget'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', 'rem'],
                'selectors' => [
                    '{{WRAPPER}} .leaflet-container' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'border_color',
            [
                'label' => __('Border Color', 'leaflet-elementor-widget'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .leaflet-container' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'border_style',
            [
                'label' => __('Border Style', 'leaflet-elementor-widget'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'none' => __('None', 'leaflet-elementor-widget'),
                    'solid' => __('Solid', 'leaflet-elementor-widget'),
                    'dashed' => __('Dashed', 'leaflet-elementor-widget'),
                    'dotted' => __('Dotted', 'leaflet-elementor-widget'),
                    'double' => __('Double', 'leaflet-elementor-widget'),
                    'groove' => __('Groove', 'leaflet-elementor-widget'),
                    'ridge' => __('Ridge', 'leaflet-elementor-widget'),
                    'inset' => __('Inset', 'leaflet-elementor-widget'),
                    'outset' => __('Outset', 'leaflet-elementor-widget'),
                ],
                'selectors' => [
                    '{{WRAPPER}} .leaflet-container' => 'border-style: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'zoom_section',
            [
                'label' => __('Zoom', 'leaflet-elementor-widget'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'zoom',
            [
                'label' => __('Zoom', 'leaflet-elementor-widget'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 13,
                'min' => 1,
                'max' => 18,
                'step' => 1,
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $latitude = isset($settings['latitude']) ? $settings['latitude'] : '';
        $longitude = isset($settings['longitude']) ? $settings['longitude'] : '';
        $popup_content = isset($settings['popup_content']) ? $settings['popup_content'] : 'Your location';
        $height = isset($settings['height']) ? $settings['height'] : 500;
        $height_mobile = isset($settings['height_mobile']) ? $settings['height_mobile'] : 300;
        $height_tablet = isset($settings['height_tablet']) ? $settings['height_tablet'] : 350;
        $height_tablet_horizontal = isset($settings['height_tablet_horizontal']) ? $settings['height_tablet_horizontal'] : 350;
        $height_laptop = isset($settings['height_laptop']) ? $settings['height_laptop'] : 400;
        $height_unit = isset($settings['height_unit']) ? $settings['height_unit'] : 'px';
        $width = isset($settings['width']) ? $settings['width'] : 600;
        $width_unit = isset($settings['width_unit']) ? $settings['width_unit'] : 'px';
        $tiles_provider = isset($settings['tiles_provider']) ? $settings['tiles_provider'] : 'OpenStreetMap';
        $enable_fullscreen = isset($settings['enable_fullscreen']) ? $settings['enable_fullscreen'] : 'yes';
        $zoom = isset($settings['zoom']) ? $settings['zoom'] : 13;
        $attribution_text = $settings['attribution_text'] ?: '© <a href=\"https://www.openstreetmap.org/copyright\">OpenStreetMap</a> contributors';

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

        $border_radius = isset($settings['border_radius']) ? $settings['border_radius'] : '';
        $border_width = isset($settings['border_width']) ? $settings['border_width'] : '';
        $border_color = isset($settings['border_color']) ? $settings['border_color'] : '';
        $border_style = isset($settings['border_style']) ? $settings['border_style'] : '';

        $border_style_string = '';
        if (!empty($border_radius['size'])) {
            $border_style_string .= 'border-radius: ' . $border_radius['size'] . $border_radius['unit'] . ';';
        }
        if (!empty($border_width['top']) || !empty($border_width['right']) || !empty($border_width['bottom']) || !empty($border_width['left'])) {
            $border_style_string .= 'border-width: ' . $border_width['top'] . $border_width['unit'] . ' ' . $border_width['right'] . $border_width['unit'] . ' ' . $border_width['bottom'] . $border_width['unit'] . ' ' . $border_width['left'] . $border_width['unit'] . ';';
        }
        if (!empty($border_color)) {
            $border_style_string .= 'border-color: ' . $border_color . ';';
        }
        if (!empty($border_style)) {
            $border_style_string .= 'border-style: ' . $border_style . ';';
        }

        if (is_numeric($latitude) && is_numeric($longitude)) {

            echo '<style>
    @media (max-width: 767px) {
        #' . esc_attr($this->map_id). ' {
            height: ' . esc_attr($height_mobile) . esc_attr($height_unit) . ' !important;
        }
    }
    
    @media (min-width: 768px) and (max-width: 1024px) {
        #' . esc_attr($this->map_id) . ' {
            height: ' . esc_attr($height_tablet) . esc_attr($height_unit) . ' !important;
        }
    }
    
    @media (min-width: 1024px) and (max-width: 1200px) {
        #' . esc_attr($this->map_id) . ' {
            height: ' . esc_attr($height_tablet_horizontal) . esc_attr($height_unit) . ' !important; 
        }
    }
    
    @media (min-width: 1200px) and (max-width: 1366px) {
        #' . esc_attr($this->map_id) . ' {
            height: ' . esc_attr($height_laptop) . esc_attr($height_unit) . ' !important; 
        }
    }
    </style>';

            echo '<div id="' . esc_attr($this->map_id) . '" class="leaflet-map" style="height: ' . esc_attr($height) . esc_attr($height_unit) . '; width: ' . esc_attr($width) . esc_attr($width_unit) . '; ' . esc_attr($border_style_string) . '"></div>';
            echo '<script>
    jQuery(document).ready(function($) {
        var map = L.map("' . esc_js($this->map_id) . '").setView([' . esc_js($latitude) . ', ' . esc_js($longitude) . '], ' . esc_js($zoom) . ');
        L.tileLayer("' . $tiles_url . '", {
            attribution: "' . $attribution_text . '"
        }).addTo(map);
    
        L.marker([' . esc_js($latitude) . ', ' . esc_js($longitude) . ']).addTo(map)
            .bindPopup("' . str_replace(array("\r\n", "\r", "\n"), "<br>", addslashes($popup_content)) . '", { autoClose: false });
            ';

            if ($enable_fullscreen == 'yes') {
                echo 'L.control.fullscreen({ position: "topleft" }).addTo(map);';
            }

            echo '});
    </script>';
        } else {
            echo '<p>Invalid coordinates</p>';
        }
    }
}
