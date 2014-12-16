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
//JHTML::_('behavior.tooltip');
?>
<ul class="joocompatibility" style="list-style:none; margin-left:0;">
<?php
	foreach ($this->params->selectedOptions as $compat) {
		echo '<li style="float:left; margin-left:0; margin-right:10px;">'.JHTML::image(JURI::root() . $this->params->path.$compat, JText::_(basename($compat,'.png'))).'</li>';
	}
?>
</ul> 
