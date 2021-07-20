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

/**
 *  Convert Forms Field Choice Class
 *  Used by dropdown and checkbox fields
 */
class FieldChoice extends \ConvertForms\Field
{
	/**
	 *  Remove common fields from the form rendering
	 *
	 *  @var  mixed
	 */
	protected $excludeFields = array(
        'browserautocomplete',
        'inputmask',
        'readonly'
	);

	/**
	 *  Set field object
	 *
	 *  @param  mixed  $field  Object or Array Field options
	 */
	public function setField($field)
	{
        parent::setField($field);

        // Get input options
        $this->field->choices = $this->getChoices();

        // The value property is very likely to be missing from the Checkbox and Radio fields as it wasn't an option since v2.2.2.
        // So add it, to prevent PHP warnings. Consider removing this check at the end of 2019 when most of the users have updated the extension.
        if (!isset($this->field->value))
        {
            $this->field->value = '';
        }

        // Use the selected choice as the field value if we don't have a default value set.
        if ($this->field->value == '')
        {
            foreach ($this->field->choices as $choice)
            {
                if ($choice['selected'])
                {
                    $this->field->value = $choice['value'];
                    break;
                }
            }
        }

        if ($this->multiple && !is_array($this->field->value))
        {
            $this->field->value = explode(',', $this->field->value);
        }

		return $this;
	}

	/**
	 *  Set the field choices
	 *
	 *  Return Array sample
	 *
	 *  $choices = array(
     *  	'label'      => 'Color',
     *   	'value'      => 'color,
     *   	'calc-value' => '150r,
     *  	'selected'   => true,
     *   	'disabled'   => false
	 *  )
	 *
	 *  @return  array  The field choices array
	 */
	protected function getChoices()
	{
        $field = $this->field;

		if (!isset($field->choices) || !isset($field->choices['choices']))
        {
            return;
        }

        $choices = array();
        $hasPlaceholder = (isset($field->placeholder) && !empty($field->placeholder));

        // Create a new array of valid only choices
        foreach ($field->choices['choices'] as $key => $choiceValue)
        {
            if (!isset($choiceValue['label']) || $choiceValue['label'] == '')
            {
                continue;
            }

            $label = trim($choiceValue['label']);
            $value = $choiceValue['value'] == '' ? strip_tags($label) : $choiceValue['value'];

            $choices[] = array(
                'label'      => $label,
                'value'      => $value,
                'calc-value' => (isset($choiceValue['calc-value']) && $choiceValue['calc-value'] != '' ? $choiceValue['calc-value'] : $value),
                'selected'   => (isset($choiceValue['default']) && $choiceValue['default'] && !$hasPlaceholder) ? true : false
            );
        }

        // If we have a placeholder available, add it to dropdown choices.
        if ($hasPlaceholder)
        {
            array_unshift($choices, array(
                'label'    => trim($field->placeholder),
                'value'    => '',
                'selected' => true,
                'disabled' => true
            ));
        }

        return $choices;
	}
}
?>