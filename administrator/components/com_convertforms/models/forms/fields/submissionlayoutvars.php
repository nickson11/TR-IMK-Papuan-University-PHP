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

defined('_JEXEC') or die('Restricted access');

use ConvertForms\FieldsHelper;

class JFormFieldSubmissionLayoutVars extends JFormField
{
    /**
     *  Disable field label
     *
     *  @return  void
     */
    protected function getLabel()
    {
        return;
    }

    /**
     *  Get field input
     *
     *  @return  string
     */
    protected function getInput()
    {
        // echo NRFramework\HTML::smartTagsBox();

        // JFactory::getDocument()->addStyleDeclaration('
        //     .CodeMirror {
        //         min-height: auto;
        //         width:800px;
        //         height:300px;
        //     }
        //     .controls > p.label {
        //         display:none;
        //     }
        // ');

        $groups = [
            'Container Layout' => [
                '{total}' => 'The total number of submissions',
                '{submissions}' => 'Contains the HTML of all submission rows.',
                '{pagination.links}' => 'Display the Pages Links.',
                '{pagination.results}' => 'Show the results currently being displayed. Eg: Results 1 - 5 of 7.',
                '{pagination.counter}' => 'Show the current page and total pages. Eg: Page 1 of 2.'
            ],
            'Row & Details Layout' => [
                '{submission.id}' => 'The ID of the submission.',
                '{submission.created}' => 'The date when the submission created.',
                '{submission.modified}' => 'The date when the submission modified.',
                '{submission.form_id}' => 'The ID of the form assosiated with the submission.',
                '{submission.visitor_id}' => 'The unique ID of the user who submitted the form.',
                '{submission.user_id}' => 'The Joomla User ID of the user who submitted the form.',
                '{link}' => 'The link to the submission details page.',
                '{field.FIELD_KEY}' => 'Use this syntax to display a field value as plain text. Eg: {field.name} or {field.myfield}',
                '{field.FIELD_KEY.html}' => 'Use this syntax to display a field value as HTML (If applicable). Eg: {field.uploadfield.html}',
            ]
        ];

        // Global Tags
        $st = new NRFramework\SmartTags;
        $global_tags = $st->get();

        foreach ($global_tags as $tag => $tag_value)
        {
            if (strpos($tag, 'querystring') !== false)
            {
                continue;
            }

            $groups['Global'][$tag] = JText::_('NR_TAG_' . strtoupper(str_replace(array("{", "}", "."), "", $tag)));
        }

        $groups['Global']['{querystring.PARAM}'] = 'Use this syntax to pull the value of a query string parameter. Eg: {querystring.id} or {querystring.name}';

        $html = '
            <div class="smarttags" style="margin-left:-180px">
            <h2>Smart Tags</h2>
            <table class="table">
        ';

        foreach ($groups as $group_key => $tags)
        {
            $html .= '<tr><th colspan="2">' . $group_key . '</th></tr>';

            foreach ($tags as $key => $value)
            {
                $html .= '<tr><td width="200px">' . $key . '</td><td>' . $value . '</td></tr>';
            }
        }

        $html .= '</table></div>';

    	return $html;
    }
}