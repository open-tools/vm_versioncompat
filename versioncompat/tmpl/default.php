<?php
/*------------------------------------------------------------------------
# versioncompat - Custom field for Virtuemart
# ------------------------------------------------------------------------
# author    Reinhold Kainhofer, Jeremy Magne
# copyright Copyright (C) 2014 OpenTools.net. All Rights Reserved.
# copyright Copyright (C) 2010 Daycounts.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
-------------------------------------------------------------------------*/
defined('_JEXEC') or 	die();
// Here the plugin values

$path = $viewData[0];
$compatibility = $viewData[1];
$versions = $viewData[2];
$cssclass = 'versioncompat-'.strtolower(basename($path));

static $versioncompat_css_included;
if (!$versioncompat_css_included) {
    $css =
"ul.versioncompat {
    list-style: none;
    margin-left: 0;
}
.versioncompat li {
    float: left;
    margin-left: 0;
    margin-right: 0px;
}
.versioncompat-versions {
    margin-left: 5px;
}
";
    $document = JFactory::getDocument();
    $document->addStyleDeclaration($css);
    $versioncompat_css_included = true;
}

//  style="list-style:none; margin-left:0;"
//  style="float:left; margin-left:0; margin-right:10px;"
if (count($compatibility)>0) {
?>
<ul class="versioncompat <?php echo $cssclass; ?>">
<?php
	foreach ($compatibility as $compat) {
		echo '<li>'.JHTML::image(JURI::root() . $path . DS . $compat, JText::_(basename($compat,'.png'))).'</li>';
	}
?>
</ul>
<?php
}
if (count($versions)>0) {
    echo "<span class=\"versioncompat-versions\">".join(", ", $versions)."</span>";
}
