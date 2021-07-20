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
class ConvertFormsViewSubmissions extends JViewLegacy
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
		$this->params = $this->get('Options');
		$this->submissions = $this->get('Items');
		$this->pagination = $this->params->get('show_pagination', true) ? $this->get('Pagination') : null;

		// Layout checks
		if ($this->params->get('layout_type', 'file') == 'custom')
		{
			$layout_container = $this->params->get('layout_container');
			$layout_row = $this->params->get('layout_row');
			$html = '';

			if (!empty($layout_container) && !empty($layout_row))
			{
				foreach ($this->submissions as $submission)
				{
					$output = ConvertForms\Submission::replaceSmartTags($submission, $layout_row);
					$output = str_replace('{link}', $submission->link, $output);
					$html .= $output;
				}

				$st = new \NRFramework\SmartTags();
				$st->add([
					'{submissions}' 	   => $html,
					'{total}'		 	   => $this->get('Total'),
					'{pagination.links}'   => ($this->pagination) ? $this->pagination->getPagesLinks() : '',
					'{pagination.counter}' => ($this->pagination) ? $this->pagination->getPagesCounter() : '',
					'{pagination.results}' => ($this->pagination) ? $this->pagination->getResultsCounter() : ''
				]);

				echo $st->replace($layout_container);
				return;
			}
		}

		// Display the view
		$this->setLayout($this->params->get('submissions_layout'));
		parent::display($tpl);
	}
}
