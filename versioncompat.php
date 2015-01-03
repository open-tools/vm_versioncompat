<?php
/*------------------------------------------------------------------------
# versioncompat - Custom field for Virtuemart
# ------------------------------------------------------------------------
# author    Reinhold Kainhofer, OpenTools
# copyright (C) 2014 OpenTools.net, Reinhold Kainhofer. All rights reserved.
#
# Based on the Joocompatibility plugin by DayCounts
# copyright Copyright (C) 2010 Daycounts.com. All Rights Reserved.
#
#
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
-------------------------------------------------------------------------*/
defined('_JEXEC') or 	die( 'Direct Access to ' . basename( __FILE__ ) . ' is not allowed.' ) ;

if (!class_exists('vmCustomPlugin')) require(JPATH_VM_PLUGINS . DS . 'vmcustomplugin.php');


class plgVmCustomVersionCompat extends vmCustomPlugin {

    function __construct(& $subject, $config) {
        parent::__construct($subject, $config);

        $this->varsToPush = array(
            'compatibility'=>array(array(), 'array'),
            'versions'=>array(array(), 'array'),
            'searchable'=>array(0,'int'),
            'directory'=>array('', 'string'),
            'textversions'=>array('', 'string'),
        );

        if(!defined('VM_VERSION') or VM_VERSION < 3){
            $this->setConfigParameterable ('custom_params', $this->varsToPush);
        } else {
            $this->setConfigParameterable ('customfield_params', $this->varsToPush);
        }

    }

    function getImages($path) {
        jimport('joomla.filesystem.file');
        jimport('joomla.filesystem.folder');

        if (!JFolder::exists($path))
            $path = JPATH_ROOT . DS . $path;
        if (!JFolder::exists($path)) continue;
        $images = JFolder::files($path, '.png');
        return $images;
    }


    // get product param for this plugin on edit
    function plgVmOnProductEdit($field, $product_id, &$row, &$retValue) {
		if ($field->custom_element != $this->_name) return '';
        if(!defined('VM_VERSION') or VM_VERSION < 3){
            $this->parseCustomParams ($field); // Not needed in VM3!
            $paramName = 'custom_param';
        } else {
            $paramName = 'customfield_params';
        }
        $html  = '';
		$html .='<fieldset>';
// 		$html .= '<legend>'. JText::_('VMCUSTOM_VERSIONCOMPAT') .'</legend>';
		$html .= '<table class="admintable">
			';
        if (!empty($field->directory)) {
            $images = $this->getImages($field->directory);
            $logos = array();
            foreach ($images as $logo) {
                $logos[] = JHTML::_('select.option', $logo, strtoupper(basename($logo,'.png')));
            }

            $html .= VmHTML::row('genericlist', 'VMCUSTOM_VERSIONCOMPAT_IMAGES', $logos, $paramName.'['.$row.'][compatibility][]', ' size="5" multiple data-placeholder="' . JText::_('VMCUSTOM_VERSIONCOMPAT_NONE') . '" ', 'value', 'text', $field->compatibility);
        }

        if (!empty($field->textversions)) {
            $textversions = explode(",", $field->textversions);
            $logos = array();
            foreach ($textversions as $ver) {
                $v = trim($ver);
                $versions[] = JHTML::_('select.option', $v, $v);
            }

            $html .= VmHTML::row('genericlist', 'VMCUSTOM_VERSIONCOMPAT_TEXTS', $versions, $paramName.'['.$row.'][versions][]', ' size="5" multiple data-placeholder="' . JText::_('VMCUSTOM_VERSIONCOMPAT_NONE') . '" ', 'value', 'text', $field->versions);
        }
        $html .= '</table></fieldset>';

        $retValue .= $html;
        $row++;
        return true;
    }

	function displayProductFE ($product, &$group) {
        // default return if it's not this plugin
        if ($group->custom_element != $this->_name) return '';
        if (!defined('VM_VERSION') or VM_VERSION < 3) { // VM2
            $this->parseCustomParams($group);
        }

        jimport('joomla.filesystem.file');
        jimport('joomla.filesystem.folder');
        $group->display .=  $this->renderByLayout('default',array($group->directory, $group->compatibility, $group->versions) );

        return true;

	}
	/**
	 * @ idx plugin index
	 * @see components/com_virtuemart/helpers/vmCustomPlugin::onDisplayProductFE()
	 * @author Patrick Kohl
	 *  Display product
	 */
    function plgVmOnDisplayProductFE($product, &$idx, &$group) {
        return $this->displayProductFE($product, $group);
    }

    function plgVmOnDisplayProductFEVM3(&$product, &$group) {
        return $this->displayProductFE($product, $group);
    }

	/**
	 * We must reimplement this triggers for joomla 1.7
	 * vmplugin triggers note by Max Milbers
	 */
// 	public function plgVmOnStoreInstallPluginTable($psType,$name) {
// 		return $this->onStoreInstallPluginTable($psType,$name);
// 	}

	function plgVmSetOnTablePluginParamsCustom($name, $id, &$table){
		return $this->setOnTablePluginParams($name, $id, $table);
	}

    function plgVmDeclarePluginParamsCustom($psType,$name,$id, &$data){
        return $this->declarePluginParams('custom', $name, $id, $data);
    }

    function plgVmDeclarePluginParamsCustomVM3(&$data){
        return $this->declarePluginParams('custom', $data);
    }

    function plgVmGetTablePluginParams($psType, $name, $id, &$xParams, &$varsToPush){
        return $this->getTablePluginParams($psType, $name, $id, $xParams, $varsToPush);
    }

// 	/**
// 	 * Custom triggers note by Max Milbers
// 	 */
	function plgVmOnDisplayEdit($virtuemart_custom_id,&$customPlugin){
		return $this->onDisplayEditBECustom($virtuemart_custom_id,$customPlugin);
	}

}

// No closing tag