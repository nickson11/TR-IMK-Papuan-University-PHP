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
extract($displayData);

?>

<div class="cf-select <?php echo $field->size ?>">
	<select name="<?php echo $field->name ?>" id="<?php echo $field->id; ?>" 
			<?php if (isset($field->required) && $field->required) { ?>
				required
			<?php } ?>

			class="<?php echo $field->class ?>"
			style="<?php echo $field->style; ?>"
		>
		<?php foreach ($field->choices as $choiceKey => $choice) { ?>
			<option 
				value="<?php echo $choice['value'] ?>" 
				data-calc-value="<?php echo isset($choice['calc-value']) ? $choice['calc-value'] : '' ?>"
				<?php if ($choice['value'] == $field->value) { ?> selected <?php } ?>
				<?php if (isset($choice['disabled'])) { ?> disabled <?php } ?>>
				<?php echo $choice['label']; ?>
			</option>
		<?php } ?>
	</select>
</div>