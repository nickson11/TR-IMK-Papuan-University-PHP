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

namespace ConvertForms\Field;

defined('_JEXEC') or die('Restricted access');

use ConvertForms\Validate;

class Url extends \ConvertForms\Field
{
    /**
	 *  Remove common fields from the form rendering
	 *
	 *  @var  mixed
	 */
	protected $excludeFields = [
		'inputmask'
	];  

	protected $inheritInputLayout = 'text';
	
	/**
	 *  Validate field value
	 *
	 *  @param   mixed  $value           The field's value to validate
	 *
	 *  @return  mixed                   True on success, throws an exception on error
	 */
	public function validate(&$value)
	{
		parent::validate($value);

		if ($this->isEmpty($value))
		{
			return true;
		}

		if (!Validate::url($value))
		{
			$this->throwError(\JText::sprintf('COM_CONVERTFORMS_FIELD_URL_INVALID'));
		}
	}
}

?>