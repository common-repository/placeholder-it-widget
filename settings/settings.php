<?php

include(dirname(__FILE__) . '/class.settings-api.php');

class Placeholder_it_Settings extends WeDevs_Settings_API
{

    const PLUGIN_NAME = 'placeholder_it_widget';

    private static $_instance;
    public $name, $Plugin, $PageTitle, $title;

    function __construct($args = array())
    {
        parent::__construct($args);

        $this->name = isset($args['name']) ? $args['name'] : '';
        $this->PageTitle = isset($args['title']) ? $args['title'] : '';

        $this->SetPluginName($this->name);

        $this->ConfigSections();
        $this->ConfigFields();
    }

    public static function getInstance($args = array())
    {
        if (!self::$_instance) {
            self::$_instance = new Placeholder_it_Settings($args);
        }
        return self::$_instance;
    }

    public function SetPluginName($name = self::PLUGIN_NAME)
    {
        $this->Plugin = $name;
    }

    public function SetPageTitle($title = 'Placeholder.it Widget')
    {
        $this->title = $title;
    }

    /**
     * Sections
     */
    private function ConfigSections()
    {
        $pi = $this->Plugin;

        $sections = array(
            array(
                'id'    => $pi . '_basics',
                'title' => __('General Settings', 'placeholder-it')
            )
        );

        $this->set_sections($sections);
    }

    /**
     * Fields
     */
    private function ConfigFields()
    {
        $pi = $this->Plugin;

        $fields = array(
            $pi . '_basics' => array(
                array(
                    'name'    => 'default_width',
                    'label'   => __('Default Width', 'placeholder-it'),
                    'desc'    => __('Default width in pixels, default=350 (no px added!)', 'placeholder-it'),
                    'type'    => 'number',
                    'default' => 350,
                    'size' => 50
                ),
                array(
                    'name'    => 'default_height',
                    'label'   => __('Default Height', 'placeholder-it'),
                    'desc'    => __('Default height in pixels, default=350 (no px added!)', 'placeholder-it'),
                    'type'    => 'number',
                    'default' => 350,
                    'size' => 50
                ), array(
                    'name'  => 'default_img_class',
                    'label' => __('Image class(es)', 'placeholder-it'),
                    'desc'  => __('Any classes added in this field will be added to the widget classes', 'placeholder-it'),
                    'type'  => 'text',
                    'default' => ''
                ),
                array(
                    'name'    => 'default_padding',
                    'label'   => __('Image padding', 'placeholder-it'),
                    'desc'    => __('Image padding (f.e. 5px 1em), default="5px"', 'placeholder-it'),
                    'type'    => 'text',
                    'default' => '5px',
                    'size' => 50
                ),
                array(
                    'name'    => 'default_include_wrapper',
                    'label'   => __('Include wrapper class', 'placeholder-it'),
                    'desc'    => __('Include the image in a wrapper? (default=checked)', 'placeholder-it'),
                    'type'    => 'checkbox',
                    'default' => 'on'
                ), array(
                    'name'    => 'default_wrapper_class',
                    'label'   => __('Wrapper class(es)', 'placeholder-it'),
                    'desc'    => __('Additional wrapper classes, "placeholder-it-wrapper" will be included, when "include wrapper class" is checked and this value is empty', 'placeholder-it'),
                    'type'    => 'text',
                    'default' => ''
                ), array(
                    'name'    => 'default_text',
                    'label'   => __('Text (prefix)', 'placeholder-it'),
                    'desc'    => __('Any text here will be prefix in the widget settings, leave empty to use dimensions instead of text', 'placeholder-it'),
                    'type'    => 'text',
                    'default' => ''
                ), array(
                    'name'  => 'default_color',
                    'label' => __('Foreground color', 'placeholder-it'),
                    'desc'  => __('Leave blank to take the default', 'placeholder-it'),
                    'type'  => 'color',
                    'default' => ''
                ), array(
                    'name'  => 'default_bg_color',
                    'label' => __('Background color', 'placeholder-it'),
                    'desc'  => __('Leave blank to take the default', 'placeholder-it'),
                    'type'  => 'color',
                    'default' => ''
                )

            )
        );

        $this->set_fields($fields);
    }
}
