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

// load the JS needed to handle the form data and send it back to the editor
$script = '
	jQuery(function($) {
		function insertConvertFormShortcode() {
			// Get the convertform id
			convertformid = $("#jform_convertformid").val();

			window.parent.jInsertEditorText("{convertforms " + convertformid + "}", ' . json_encode($this->eName) . ');
			window.parent.jModalClose();
			return false;
		}

		$(".cfEditorButton button").click(function() {
			insertConvertFormShortcode();
		})
	})
';

$style = '
	.cfEditorButton form, .eboxEditorButton .controls > * {
		margin:0;
	}
	.cfHeader {
	    border-bottom: 1px dotted #ccc;
	    margin-bottom: 15px;
	    padding-bottom: 5px;
	}
	.cfHeader p {
	    color:#666;
	    font-size: 11px;
	}
	.cfHeader h3 {
	    font-size: 16px;
	    margin-bottom: 5px;
	    margin-top: 0;
	}
	.cfEditorButton .control-group {
	    margin-bottom: 15px;
	}
	.cfEditorButton {
	    padding: 5px;
	}
';

JFactory::getDocument()->addScriptDeclaration($script);
JFactory::getDocument()->addStyleDeclaration($style);

?>
<div class="cfEditorButton">
	<form>
		<?php echo $this->form->renderFieldset("main") ?>
		<button class="btn btn-primary span12">
			<?php echo JText::_('PLG_EDITORS-XTD_CONVERTFORMS_INSERTBUTTON'); ?>
		</button>
	</form>
</div>
