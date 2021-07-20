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

use ConvertForms\Analytics;

?>

<table width="100%" class="table nrTable">
	<tr>
		<td><?php echo JText::_("COM_CONVERTFORMS_LAST_YEAR") ?></td>
		<td class="text-right"><?php echo Analytics::getRows("lastyear") ?></td>
	</tr>
	<tr>
		<td><?php echo JText::_("COM_CONVERTFORMS_THIS_YEAR") ?></td>
		<td class="text-right"><?php echo Analytics::getRows("thisyear") ?></td>
	</tr>
	<tr>
		<td><?php echo JText::_("COM_CONVERTFORMS_LAST_MONTH") ?></td>
		<td class="text-right"><?php echo Analytics::getRows("lastmonth") ?></td>
	</tr>
	<tr>
		<td><?php echo JText::_("COM_CONVERTFORMS_THIS_MONTH") ?></td>
		<td class="text-right"><?php echo Analytics::getRows("thismonth") ?></td>
	</tr>
	<tr>
		<td><?php echo JText::_("COM_CONVERTFORMS_LAST_7_DAYS") ?></td>
		<td class="text-right"><?php echo Analytics::getRows("interval", array("interval" => "1 WEEK")) ?></td>
	</tr>
	<tr>
		<td><?php echo JText::_("COM_CONVERTFORMS_YESTERDAY") ?></td>
		<td class="text-right"><?php echo Analytics::getRows("yesterday") ?></td>
	</tr>
	<tr>
		<td><?php echo JText::_("COM_CONVERTFORMS_TODAY") ?></td>
		<td class="text-right"><?php echo Analytics::getRows("today") ?></td>
	</tr>
	<tr>
		<td><?php echo JText::_("COM_CONVERTFORMS_AVG_DAY") ?></td>
		<td class="text-right"><?php echo Analytics::getLeadsAverageThisMonth() ?></td>
	</tr>
	<tr>
		<td><?php echo JText::_("COM_CONVERTFORMS_PROJECTION") ?></td>
		<td class="text-right"><?php echo Analytics::getMonthProjection(); ?></td>
	</tr>
	<tr>
		<td><?php echo JText::_("COM_CONVERTFORMS_TOTAL") ?></td>
		<td class="text-right"><?php echo Analytics::getRows(); ?></td>
	</tr>
</table>

