<?php
require_once(dirname(__FILE__) . '/piwSettings.php');


class Placeholder_It_Widget_Widget extends WP_Widget
{
    /**
     * Holds widget settings defaults, populated in constructor.
     *
     * @var array
     */
    protected $defaults, $settings;

    /**
     * Constructor. Set the default widget options and create widget.
     */
    function __construct()
    {
        load_plugin_textdomain('placeholder-it', false, basename(dirname(__FILE__)) . '/language');
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
        add_action('wp_enqueue_scripts', array($this, 'wp_enqueue_scripts'));

        $section = 'placeholder_it_widget_basics';

        $this->defaults = array(
            'title'               => '',
            'piw_img_class'       => $this->get_option('default_img_class', $section, ''),
            'piw_width'           => $this->get_option('default_width', $section, 350),
            'piw_height'          => $this->get_option('default_height', $section, 350),
            'piw_color'           => $this->get_option('default_color', $section, ''),
            'piw_bg_color'        => $this->get_option('default_bg_color', $section, ''),
            'piw_text'            => $this->get_option('default_text', $section, ''),
            'piw_include_wrapper' => $this->get_option('default_include_wrapper', $section, 'on'),
            'piw_padding'         => $this->get_option('default_padding', $section, '5px'),
            'piw_wrapper_class'   => $this->get_option('default_wrapper_class', $section, '')
        );

        $widget_ops = array(
            'classname'   => 'placeholder-it',
            'description' => __('Placehold.it Placeholder', 'placeholder-it'),
        );
        $control_ops = array(
            'id_base' => 'placeholder-it-widget',
            'width'   => 400,
            'height'  => 350
        );

        parent::__construct(
            'placeholder-it-widget',
            __('Placeholder It Widget', 'placeholder-it'),
            $widget_ops,
            $control_ops
        );
    }

    public function admin_enqueue_scripts()
    {
        // Loads the widget style.
        wp_enqueue_style('placeholder-it-admin-style', plugin_dir_url(__FILE__) . '../admin.css', null, null);
        /**
         * "underscore" and the "wp-color-picker" is required for the piwSettings.php !!!!
         */
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
        wp_enqueue_script('underscore');
    }

    public function wp_enqueue_scripts()
    {
        wp_enqueue_style('placeholder-it-style', plugin_dir_url(__FILE__) . '../css/style.css', null, null);
    }

    /**
     * Echo the widget content.
     *
     * @param array $args Display arguments including before_title, after_title, before_widget, and after_widget.
     * @param array $instance The settings for the particular instance of the widget
     */
    function widget($args, $instance)
    {
        extract($args);

        //* Merge with defaults
        $instance = wp_parse_args((array)$instance, $this->defaults);

        echo $before_widget;

        //* Set up the title

        if (!empty($instance['title'])) {
            $bexplode = explode(' ', $before_title);
            $before_title = str_replace('%', ' ', implode('%', $bexplode));

            printf('%s%s%s',
                $before_title,
                apply_filters('widget_title', $instance['title'], $instance, $this->id_base),
                $after_title
            );
        }

        /* Make sure we have some values */
        $width = intval($instance['piw_width']) > 1 ? intval($instance['piw_width']) : 350;
        $height = intval($instance['piw_height']) > 1 ? intval($instance['piw_height']) : 350;
        $imgCls = trim($instance['piw_img_class']) != '' ? "class=\"{$instance['piw_img_class']}\"" : '';

        $padding = trim($instance['piw_padding']);
        $_padding = $padding !== '' ? " style=\"padding:{$padding}\"" : '';

        $includeWrap = $instance['piw_include_wrapper'] === 'on';

        $wrap = '';
        if ($includeWrap) {
            $wrapClass = trim($instance['piw_wrapper_class']) != '' ? $instance['piw_wrapper_class'] : 'placeholder-it-wrapper';
            $wrap = sprintf("<div class=\"%s\">",
                $wrapClass
            );
        }

        $color = $bg_color = false;

        // foreground color
        if (!empty($instance['piw_color'])) {
            $color = str_replace('#', '', "/{$instance['piw_color']}");
        }

        // background color
        if (!empty($instance['piw_bg_color'])) {
            $bg_color = str_replace('#', '', "/{$instance['piw_bg_color']}");
        }

        $text = !empty($instance['piw_text']) ? str_replace(' ', '+', "?text={$instance['piw_text']}") : '';

        if ($color && !$bg_color) {
            $bg_color = '/CCCCCC';
        }

        if ($bg_color && !$color) {
            $color = '/A2A2A2';
        }

        printf('%s<img %s %s src="http://placehold.it/%sx%s%s%s%s">%s',
            $includeWrap ? $wrap : '',
            $imgCls,
            $_padding,
            $width,
            $height,
            $bg_color ? $bg_color : '',
            $color ? $color : '',
            $text,
            $includeWrap ? '</div>' : ''
        );

        echo $after_widget;
    }

    /**
     * Update widget settings
     *
     * @param array $new_instance New settings for this instance as input by the user via form()
     * @param array $old_instance Old settings for this instance
     *
     * @return array Settings to save or bool false to cancel saving
     */
    function update($new_instance, $old_instance)
    {
        $new_instance['title'] = strip_tags($new_instance['title']);
        $new_instance['piw_include_wrapper'] = strip_tags($new_instance['piw_include_wrapper']);
        return $new_instance;
    }

    /**
     * Echo the settings update form.
     *
     * @param array $instance Current settings
     */
    function form($instance)
    {
        //* Merge with defaults
        $instance = wp_parse_args((array)$instance, $this->defaults);

        // Parameters: text_domain, $this, $instance);
        $Settings = new piwSettings('placeholder-it', $this, $instance);

        $Settings->Text(array(
            'id'     => 'title',
            'label'  => __('Title', 'placeholder-it'),
            'class'  => 'widefat',
            'before' => '<p>',
            'after'  => '</p>'
        ));
        $Settings->Text(array(
            'id'     => 'piw_width',
            'label'  => __('Width', 'placeholder-it'),
            'before' => '<p>',
            'after'  => '</p>'
        ));
        $Settings->Text(array(
            'id'     => 'piw_height',
            'label'  => __('Height', 'placeholder-it'),
            'before' => '<p>',
            'after'  => '</p>'
        ));
        $Settings->Text(array(
            'id'     => 'piw_padding',
            'label'  => __('Image padding', 'placeholder-it'),
            'before' => '<p>',
            'after'  => '</p>',
            'class'  => 'widefat'
        ));
        $Settings->Text(array(
            'id'     => 'piw_img_class',
            'label'  => __('Image extra CSS Classes', 'placeholder-it'),
            'before' => '<p>',
            'after'  => '</p>',
            'class'  => 'widefat',
            'remark' => __('Leave empty for no extra classes', 'placeholder-it')
        ));
        $Settings->Checkbox(array(
            'id'      => 'piw_include_wrapper',
            'label'   => __('Surround image with wrapper', 'placeholder-it'),
            'before'  => '<p>',
            'after'   => '</p>',
            'remark'  => __('The image is bound in a "div" wrapper', 'placeholder-it')
        ));
        $Settings->Text(array(
            'id'     => 'piw_wrapper_class',
            'label'  => __('Wrapper CSS Classes', 'placeholder-it'),
            'before' => '<p>',
            'after'  => '</p>',
            'class'  => 'widefat',
            'remark' => __('When "wrapper" is checked and this value is empty, "placeholder-it-wrapper" will be used as class', 'placeholder-it')
        ));
        $Settings->Text(array(
            'id'     => 'piw_text',
            'label'  => __('Text', 'placeholder-it'),
            'before' => '<p>',
            'after'  => '</p>',
            'class'  => 'widefat',
            'remark' => __('Show this text instead of dimensions', 'placeholder-it')
        ));
        $Settings->Colorpicker(array(
            'id'     => 'piw_color',
            'label'  => __('Text color', 'placeholder-it'),
            'before' => '<p>',
            'after'  => '</p>',
            'remark' => __('Enter a color color code like "#FF9000", or leave empty for default', 'placeholder-it')
        ));
        $Settings->Colorpicker(array(
            'id'     => 'piw_bg_color',
            'label'  => __('Background color', 'placeholder-it'),
            'before' => '<p>',
            'after'  => '</p>',
            'remark' => __('Same as Text color, takes a default if color is entered and this field is empty', 'placeholder-it')
        ));
    }

    /**
     * Gets the options from the WADEV Settings
     * @param $option
     * @param $section
     * @param string $default
     *
     * @return string
     */
    function get_option($option, $section, $default = '')
    {
        $options = get_option($section);

        if (isset($options[$option])) {
            return $options[$option];
        }

        return $default;
    }
}