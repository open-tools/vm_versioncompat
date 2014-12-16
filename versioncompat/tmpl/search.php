<?php 
/*------------------------------------------------------------------------
# joocompatibility - Custom field for Virtuemart
# ------------------------------------------------------------------------
# author    Jeremy Magne
# copyright Copyright (C) 2010 Daycounts.com. All Rights Reserved.
# Websites: http://www.daycounts.com
# Technical Support: http://www.daycounts.com/en/contact/
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
-------------------------------------------------------------------------*/
defined('_JEXEC') or 	die(); 
// Here the plugin values
?>
<?php
	echo $this->params->custom_title.'&nbsp;:&nbsp;';
	foreach ($this->params->images as $image) { 
		if (in_array($image,$this->params->searched)) {
			$checked = 'checked="checked"';
		} else {
			$checked = '';
		}
	?>
    	<label><input type="checkbox" value="<?php echo $image ?>" name="joocompatibility[]" <?php echo $checked; ?>><?php echo JHTML::image(JURI::root() . $this->params->path.$image, basename($image,'.png')); ?></label>
        <?php
	}
?>
<br />
