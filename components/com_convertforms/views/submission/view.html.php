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

defined('_JEXEC') or die;

/**
 * Content categories view.
 *
 * @since  1.5
 */
class ConvertFormsViewSubmission extends JViewLegacy
{
	/**
	 * Display the Hello World view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
		if (!$this->submission = $this->get('Item'))
		{
			JFactory::getApplication()->enqueueMessage(JText::_('COM_CONVERTFORMS_SUBMISSION_INVALID'), 'error');
			return;
		}

		$this->params = $this->get('Options');

		// Layout checks
		if ($this->params->get('layout_type', 'file') == 'custom')
		{
			$layout = $this->params->get('layout_details');

			if (!empty($layout))
			{
				echo ConvertForms\Submission::replaceSmartTags($this->submission, $layout);
				return;
			}
		}

		$this->menu = $this->get('Menu');
		$this->submissions_link = JRoute::_('index.php?option=com_convertforms&view=submissions&Itemid=' . $this->menu->id);

		// Display the view
		$this->setLayout($this->params->get('submissions_layout'));
		parent::display($tpl);
	}
}
