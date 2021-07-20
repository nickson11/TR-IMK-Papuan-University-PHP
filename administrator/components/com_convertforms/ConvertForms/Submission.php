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

class Submission
{
    public static function getSmartTags($submission)
    {
        $data = [
            'submission' => [
                'id'          => $submission->id,
                'date'        => $submission->created,
                'campaign_id' => $submission->campaign_id,
                'form_id'     => $submission->form_id,
                'visitor_id'  => $submission->visitor_id
            ]
        ];

        if (!is_array($submission->prepared_fields))
        {
            return;
        }

        $fields = $submission->prepared_fields;

        $all_fields = '';
        $all_fields_filled = '';

        foreach ($fields as $field)
        {
            $field_name = $field->options->get('name');

            // In case of a dropdown and radio fields, make also the label and the calc-value properties available. 
            // This is rather useful when we want to display the dropdown's selected text rather than the dropdown's value.
            if (in_array($field->options->get('type'), ['dropdown', 'radio']))
            {
                foreach ($field->options->get('choices.choices') as $choice)
                {
                    $choice = (array) $choice;

                    if ($field->value !== $choice['value'])
                    {
                        continue;
                    }

                    if (isset($choice['label']))
                    {
                        $data['field'][$field_name . '.label'] = $choice['label'];
                    }

                    if (isset($choice['calc-value']))
                    {
                        $data['field'][$field_name . '.calcvalue'] = $choice['calc-value'];
                    }
                }
            }

            $data['field'][$field_name] = $field->value;                // The value in plain text. Arrays will be shown comma separated.
            $data['field'][$field_name . '.raw'] = $field->value_raw;   // The raw value as saved in the database.
            $data['field'][$field_name . '.html'] = $field->value_html; // The value as transformed to be shown in HTML.

            $all_fields_item = '<div><strong>' . $field->label . '</strong>: ' . $field->value_html . '</div>';
            $all_fields .= $all_fields_item;
            $all_fields_filled .= $field->value_html ? $all_fields_item : '';
        }

        $data['']['all_fields'] = $all_fields;
        $data['']['all_fields_filled'] = $all_fields_filled;

        \JFactory::getApplication()->triggerEvent('onConvertFormsGetSubmissionSmartTags', [$submission, &$data]);

        return $data;
    }

    public static function replaceSmartTags($submission, $layout)
    {
        $smartTagGroups = self::getSmartTags($submission);

        $st = new \NRFramework\SmartTags();

        foreach ($smartTagGroups as $key => $smartTagGroup)
        {
            $prefix = empty($key) ? null : $key . '.';
            $st->add($smartTagGroup, $prefix);
        }

        return $st->replace($layout);
    }

    public static function route($submission_id)
    {
        return \JRoute::_('index.php?option=com_convertforms&view=submission&id=' . $submission_id);
    }
}