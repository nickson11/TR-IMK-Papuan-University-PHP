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

if ($this->params->get('load_css', true))
{
	JHtml::stylesheet('com_convertforms/submissions.css', ['relative' => true, 'version' => 'auto']);
}

$show_page_heading = $this->params->get('show_page_heading') != '0';
$page_heading = $this->params->get('page_heading', JText::_('COM_CONVERTFORMS_SUBMISSIONS'));

?>
<div class="convertforms-submissions list">
	<?php if ($show_page_heading) { ?>
		<h1><?php echo $page_heading ?></h1>
	<?php } ?>
	<?php echo $this->loadTemplate(count($this->submissions) ? 'list' : 'noresults'); ?>
</div>