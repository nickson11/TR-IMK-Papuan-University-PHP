<?php

/**
 * @package         Convert Forms
 * @version         2.7.0 Free
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2020 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace ConvertForms;

defined('_JEXEC') or die('Restricted access');

use Joomla\Registry\Registry;

/**
 *  ConvertForms Fields Helper Class
 */
class FieldsHelper
{
    /**
     *  List of available field groups and types
     *
     *  Consider using a field class property in order to declare the field group instead.
     *
     *  @var  array
     */
    public static $fields = [
        'common' => [
            'text',
            'textarea',
            'dropdown',
            'radio',
            'checkbox',
            'number',
            'email',
            'submit'
        ],
        'userinfo' => [
            'tel',
            'url',
            'datetime',
            'country',
            'currency',
        ],
        'layout' => [
            'html',
            'emptyspace',
            'divider',
        ],
        'advanced' => [
            'hidden',
            'password',
            'fileupload',
            'recaptcha',
            'recaptchav2invisible',
            'termsofservice',
            'confirm'
        ]
    ];

    /**
     *  Returns a list of all available field groups and types
     *
     *  @return  array  
     */
    public static function getFieldTypes()
    {
        $arr = [];

        foreach (self::$fields as $group => $fields)
        {
            if (!count($fields))
            {
                continue;
            }

            $arr[$group] = array(
                'name'  => $group,
                'title' => \JText::_('COM_CONVERTFORMS_FIELDGROUP_' . strtoupper($group))
            );

            foreach ($fields as $key => $field)
            {
                $arr[$group]['fields'][] = array(
                    'name'  => $field,
                    'title' => \JText::_('COM_CONVERTFORMS_FIELD_' . strtoupper($field)),
                    'desc'  => \JText::_('COM_CONVERTFORMS_FIELD_' . strtoupper($field) . '_DESC'),
                    'class' => self::getFieldClass($field)
                );
            }
        }

        return $arr;
    }

    /**
     *  Render field control group used in the front-end
     *
     *  @param   object  $fields  The fields to render
     *
     *  @return  string           The HTML output
     */
    public static function render($fields)
    {
        $html = array();

        foreach ($fields as $key => $field)
        {
            if (!isset($field['type']))
            {
                continue;
            }

            // Skip unknown field types
            if (!$class = self::getFieldClass($field['type']))
            {
                continue;
            }

            $html[] = $class->setField($field)->getControlGroup();
        }

        return implode(' ', $html);
    }

    /**
     *  Constructs and returns the field type class
     *
     *  @param   String  $name  The field type name
     *
     *  @return  Mixed          Object on success, Null on failure
     */
    public static function getFieldClass($name, $field_data = null, $form_data = null)
    {
        $class = __NAMESPACE__ . '\\Field\\' . ucfirst($name);

        if (!class_exists($class))
        {
            return false;
        }

        return new $class($field_data, $form_data);
    }

    public static function prepare($form, $classPrefix = 'cf')
    {
        $params = $form['params'];

        if (!is_array($form['fields']) || count($form['fields']) == 0)
        {
            return;
        }

        $fields_ = [];

        foreach ($form['fields'] as $key => $field)
        {
            $field['namespace'] = $form['id'];

            // Labels Styles
            $field['labelStyles'] = array(
                "color:"     . $params->get("labelscolor"),
                "font-size:" . (int) $params->get("labelsfontsize") . "px"
            );

            // Field Classes
            $fieldClasses = [
                $classPrefix . "-input",
                $classPrefix . "-input-shadow-" . ($params->get("inputshadow", "false") ? "1" : "0"),
                isset($field['size']) ? $field['size'] : null,
                isset($field['inputcssclass']) ? $field['inputcssclass'] : null
            ];

            // Field Styles
            $fieldStyles = array(
                "text-align:"       . $params->get("inputalign", "left"),
                "color:"            . $params->get("inputcolor", "#888"),
                "background-color:" . $params->get("inputbg"),
                "border-color:"     . $params->get("inputbordercolor", "#ccc"),
                "border-radius:"    . (int) $params->get("inputborderradius", "0") . "px",
                "font-size:"        . (int) $params->get("inputfontsize", "13") . "px",
                "padding:"          . (int) $params->get("inputvpadding", "11") . "px " . (int) $params->get("inputhpadding", "12") . "px"
            ); 

            $field['class'] = implode(' ', $fieldClasses);
            $field['style'] = implode(';', $fieldStyles);
            $field['form']  = $form;

            $fields_[] = $field;
        }

        $html = self::render($fields_);

        return $html;
    }
}

?>