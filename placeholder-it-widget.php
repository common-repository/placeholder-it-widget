<?php

include(dirname(__FILE__) . '/settings/settings.php');
/*
  Plugin Name: Placeholder.it widget
  Plugin URI: http://www.enovision.nl/Placeholder_It_Widget_widget
  Description: Simple widget to add placeholders using placeholder.it webservice during development of a site.
  Version: 0.9
  Author: Johan van de Merwe
  Author URI: http://www.enovision.net
*/

$Placeholder_It_Widget = new Placeholder_It_Widget();

class Placeholder_It_Widget
{
    const PLUGIN_NAME = 'placeholder_it_widget';
    const PLUGIN_TITLE = 'Placeholder.it Widget';
    const DEFAULT_LANG = 'en';

    var $settings_api;

    function __construct()
    {
        add_action('init', array($this, 'SetLanguage'), 1);
        add_action('admin_head', array($this, 'AdminStyles'));
        add_action('admin_init', array($this, 'ConfigSettings'));
        add_action('admin_menu', array($this, 'InsertAdminMenuLink'));
        add_action('widgets_init', array($this, 'InitializeWidget'));
        add_action('admin_head-widgets.php', array($this, 'set_widget_icon'));
    }

    function SetLanguage()
    {
        load_plugin_textdomain('placeholder-it', false, basename(dirname(__FILE__)) . '/languages');
    }

    function AdminStyles()
    {
        wp_register_style('phiw-admin-styles', WP_PLUGIN_URL . '/' . basename(dirname(__FILE__)) . '/css/admin.css');
        wp_enqueue_style('phiw-admin-styles');
    }

    function ConfigSettings()
    {
        $this->settings_api = Placeholder_it_Settings::getInstance(array(
            'name'  => self::PLUGIN_NAME,
            'title' => __('Plugin Options', 'placeholder-it') . self::PLUGIN_NAME
        ));

        $this->settings_api->admin_init();
    }

    function InitializeWidget()
    {
        require_once('widget/widget.placeholder-it-widget.php');
        register_widget('Placeholder_It_Widget_Widget');
    }

    function InsertAdminMenuLink()
    {
        add_options_page(
            __(self::PLUGIN_TITLE, 'placeholder-it'),
            __(self::PLUGIN_TITLE, 'placeholder-it'),
            'manage_options',
            self::PLUGIN_NAME,
            array($this, 'ConfigPageHtml')
        );
    }

    function ConfigPageHtml()
    {

        echo '<div class="wrap placeholder-it-widget">';

        echo '<h2>' . self::PLUGIN_TITLE . ' ' . __('Settings', 'placeholder-it') . '</h2>';

        $this->settings_api->show_navigation();

        $this->settings_api->show_forms();

        echo '</div>';
    }

    function InsertSettingsLink($links)
    {
        $settings_link = '<a href="options-general.php?page=' . self::PLUGIN_NAME . '">' . __('Settings', 'placeholder-it') . '</a>';
        array_unshift($links, $settings_link);
        return $links;
    }

    function set_widget_icon()
    {
        ?>
        <style>
            *[id*="_placeholder-it-widget"] > div.widget-top > div.widget-title > h3:before {
                font-family: "dashicons";
                content: "\f489";
                width: 33px;
                float: left;
                height: 6px;
                margin-top: -5px;
                font-size: 22px;
            }
        </style>
        <?php
    }
}