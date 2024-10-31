<?php

class piwSettings
{
    var $domain;
    var $widget;
    var $instance;

    var $hasColorScript = false;

    public function __construct($domain, $widget, $instance)
    {
        if (isset($domain)) $this->domain = $domain;
        if (isset($widget)) $this->widget = $widget;
        if (isset($instance)) $this->instance = $instance;
    }

    /**
     * Prints a label to a field
     *
     * @param array $args
     *
     * @return bool
     */
    public function Label($args = array())
    {

        if (!$this->validate($args, array('for', 'label'))) return false;

        $this->doBefore($args);

        printf('<label for="%s">%s:&nbsp;</label>',
            $args['for'],
            $args['label'],
            $this->domain
        );

        $this->doAfter($args);

    }

    /**
     * Shows a textfield
     *
     * @param array $args
     *
     * @return bool
     */
    public function Text($args = array())
    {
        if (!$this->validate($args, array('id'))) return false;

        $this->doBefore($args);

        if (isset($args['label'])) {
            $this->Label(array(
                'for'   => $this->widget->get_field_id($args['id']),
                'label' => $args['label']
            ));
        }

        printf('<input type="text" id="%s" name="%s" value="%s" %s/>',
            $this->widget->get_field_id($args['id']),
            $this->widget->get_field_name($args['id']),
            esc_attr($this->instance[$args['id']]),
            isset($args['class']) ? 'class="' . $args['class'] . '"' : ''
        );

        $this->doRemark($args);

        $this->doAfter($args);

    }

    /**
     *
     * Shows a checkbox that can be selected or not
     *
     * @param array $args
     *
     * @return bool
     */
    public function Checkbox($args = array())
    {
        if (!$this->validate($args, array('id'))) return false;

        $this->doBefore($args);

        printf('<input type="checkbox" id="%s" name="%s" %s %s %s>%s</input>',
            $this->widget->get_field_id($args['id']), // id
            $this->widget->get_field_name($args['id']), // name
            $this->instance[$args['id']] === 'on' ? 'value="on"' : '', // value
            checked($this->instance[$args['id']], 'on', false), // checked=checked
            isset($args['class']) ? 'class="' . $args['class'] . '"' : '', // classes
            $args['label'] // label
        );

        $this->doRemark($args);

        $this->doAfter($args);

    }

    /**
     *
     * Shows a colorpicker
     *
     * @param array $args
     *
     * @return string
     */
    public function Colorpicker($args = array())
    {
        if (!$this->validate($args, array('id'))) return false;

        $this->doBefore($args);

        $this->setColorScript();

        if (isset($args['label'])) {
            $this->Label(array(
                'for'   => $this->widget->get_field_id($args['id']),
                'label' => $args['label'],
                'after' => '<br/>'
            ));
        }

        printf('<input type="text" id="%s" class="piw-color-picker-field" name="%s" value="%s" %s/>',
            $this->widget->get_field_id($args['id']),
            $this->widget->get_field_name($args['id']),
            esc_attr($this->instance[$args['id']]),
            isset($args['class']) ? 'class="' . $args['class'] . '"' : ''
        );

        $this->doRemark($args);

        $this->doAfter($args);

    }

    /**
     * Validate the required $args entries
     *
     * @param $args
     * @param $required
     *
     * @return bool
     */
    protected function validate($args, $required)
    {
        foreach ($required as $req) {
            if (!isset($args[$req])) {
                printf('### Not exists: %s ###<br/>', $req);
                return false;
            }
        }
        return true;
    }

    /**
     * Adds the remark argument after the field
     *
     * @param $args
     */
    private function doRemark($args)
    {

        if (isset($args['remark'])) {
            printf('<br/><i>%s</i>',
                $args['remark']
            );
        }
    }

    /**
     * Echo before argument if set
     *
     * @param $args
     */
    protected function doBefore($args)
    {
        if (isset($args['before'])) {
            $this->output($args['before']);
        }
    }

    /**
     * Echo after argument if set
     *
     * @param $args
     */
    protected function doAfter($args)
    {
        if (isset($args['after'])) {
            $this->output($args['after']);
        }
    }

    /**
     * Echo string
     *
     * @param $string
     */
    public function output($args = false)
    {
        if (is_string($args)) {
            echo $args;
        } elseif (is_array($args)) {

            if (!$this->validate($args, array('text'))) return false;

            $this->doBefore($args);
            echo $args['text'];
            $this->doAfter($args);
        }
    }

    private function setColorScript()
    {
        if ($this->hasColorScript) return;
        ?>

        <script type='text/javascript'>
            ( function (jQuery) {
                function initColorPicker(widget) {
                    widget.find('.piw-color-picker-field').wpColorPicker({
                        change: _.throttle(function () { // For Customizer
                            jQuery(this).trigger('change');
                        }, 3000)
                    });
                }

                function onFormUpdate(event, widget) {
                    initColorPicker(widget);
                }

                jQuery(document).on('widget-added widget-updated', onFormUpdate);

                jQuery(document).ready(function () {
                    jQuery('#widgets-right .widget:has(.piw-color-picker-field)').each(function () {
                        initColorPicker(jQuery(this));
                    });
                });
            }(jQuery) );
        </script>

        <?php
        $this->hasColorScript = true;
    }
}