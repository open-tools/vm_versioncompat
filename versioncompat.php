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

		$this->_tablepkey = 'id';
		$this->tableFields = array_keys($this->getTableSQLFields());
		$this->varsToPush = array('compatibility'=> array('', 'string'),'searchable'=>array(0,'int'));

		$this->setConfigParameterable('custom_params',$this->varsToPush);

	}
	/**
	 * Create the table for this plugin if it does not yet exist.
	 */
	public function getVmPluginCreateTableSQL() {
		return $this->createTableSQL('Joomla Compatibility Table');
	}

	function getTableSQLFields() {
		$SQLfields = array(
		    'id' => 'bigint(20) unsigned NOT NULL AUTO_INCREMENT',
		    'virtuemart_custom_id' => 'int(11) UNSIGNED DEFAULT NULL',
		    'virtuemart_product_id' => 'int(11) UNSIGNED DEFAULT NULL',
		    'compatibility' => 'varchar(50) NOT NULL DEFAULT \'\' ',
		);

		return $SQLfields;
	}
	
	function getImages() {
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		$path = __DIR__.DS.'joocompatibility';
		$images = JFolder::files($path,'.png');
		return $images;
	}


	// get product param for this plugin on edit
	function plgVmOnProductEdit($field, $product_id, &$row,&$retValue) {
		if ($field->custom_element != $this->_name) return '';
		$this->getCustomParams($field);
		$this->getPluginCustomData($field, $product_id);
		$images = $this->getImages();
		
		$logos = array();
		foreach ($images as $logo) {
			$logos[] = JHTML::_('select.option', $logo, strtoupper(basename($logo,'.png')));
		}

		$id = $this->getIdForCustomIdProduct ($product_id, $field->virtuemart_custom_id);
		$datas = $this->getPluginInternalData ($id);
		$selectedOptions = explode($this->glue,$datas->compatibility);

		$html ='<div>';
		$html .= '<input type="hidden" name="plugin_param['.$row.']['.$this->_name.'][virtuemart_custom_id]" value="'.$field->virtuemart_custom_id.'" />';
		$html .= JHTML::_('select.genericlist', $logos, 'plugin_param['.$row.']['.$this->_name.'][compatibility][]', ' size="5" multiple="multiple"', 'value', 'text', $selectedOptions );
		$html .='</div>';
		$retValue .= $html  ;
		$row++;
		return true  ;
	}

	/**
	 * @ idx plugin index
	 * @see components/com_virtuemart/helpers/vmCustomPlugin::onDisplayProductFE()
	 * @author Patrick Kohl
	 *  Display product
	 */
	function plgVmOnDisplayProductFE($product,&$idx,&$group) {
		// default return if it's not this plugin
		if ($group->custom_element != $this->_name) return '';

		$this->_tableChecked = true;
		$this->getCustomParams($group);
		$this->getPluginCustomData($group, $product->virtuemart_product_id);
		$id = $this->getIdForCustomIdProduct ($product->virtuemart_product_id, $group->virtuemart_custom_id);
		$datas = $this->getPluginInternalData ($id);
		$this->params->selectedOptions = explode($this->glue,$datas->compatibility);

		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		$this->params->path = 'plugins/vmcustom/joocompatibility/';
		if (JFolder::exists(JPATH_SITE.DS.'plugins'.DS.'vmcustom'.DS.'joocompatibility'.DS.'joocompatibility')) {
			$this->params->path = 'plugins/vmcustom/joocompatibility/joocompatibility/';
		}
		$group->display .=  $this->renderByLayout('default',array($this->params,&$idx,&$group ) );
		
		return true;
	}

	function plgVmOnStoreProduct($data,$plugin_param){
		if (key ($plugin_param) == $this->_name) {
			if (is_array($plugin_param[$this->_name]['compatibility'])) {
				$compatibility = implode($this->glue,$plugin_param[$this->_name]['compatibility']);
			} else {
				$compatibility = $plugin_param[$this->_name]['compatibility'];
			}
			$plugin_param[$this->_name]['compatibility'] = $compatibility;
		}
		return $this->OnStoreProduct($data,$plugin_param);
	}

	public function plgVmSelectSearchableCustom(&$selectList,&$searchCustomValues,$virtuemart_custom_id)
	{
		$db =JFactory::getDBO();
		$db->setQuery('SELECT `virtuemart_custom_id`, `custom_title`, custom_params FROM `#__virtuemart_customs` WHERE `custom_element` ="'.$this->_name.'"');
		$custom_param = $db->loadObject();
		$this->selectList = $db->loadAssocList();
		VmTable::bindParameterable ($custom_param, 'custom_params', $this->_varsToPushParam);
		if (!$custom_param->searchable) {
			return;
		}

		$this->params->custom_title = $custom_param->custom_title;

		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		
		$this->params->path = 'plugins/vmcustom/joocompatibility/';
		if (JFolder::exists(JPATH_SITE.DS.'plugins'.DS.'vmcustom'.DS.'joocompatibility'.DS.'joocompatibility')) {
			$this->params->path = 'plugins/vmcustom/joocompatibility/joocompatibility/';
		}
		$this->params->images = $this->getImages();
		$this->params->searched = JRequest::getVar('joocompatibility',array());
		
		$selectList = array_merge((array)$this->selectList,$selectList);
		$searchCustomValues .=  $this->renderByLayout('search',array($this->params) );
		return true;
	}

	public function plgVmAddToSearch(&$where,&$PluginJoinTables,$custom_id)
	{
		$searched = JRequest::getVar('joocompatibility',array());
		$conditions = array();
		if (count($searched)) {
			foreach ($searched as $searchOption) {
				$conditions[] = $this->_name .'.`compatibility` LIKE "%'.$searchOption.'%"';
			}
			$where[] = implode(' OR ', $conditions);
			$PluginJoinTables[] = $this->_name ;
		}
		return true;
	}

	/**
	 * We must reimplement this triggers for joomla 1.7
	 * vmplugin triggers note by Max Milbers
	 */
	public function plgVmOnStoreInstallPluginTable($psType,$name) {
		return $this->onStoreInstallPluginTable($psType,$name);
	}

	function plgVmSetOnTablePluginParamsCustom($name, $id, &$table){
		return $this->setOnTablePluginParams($name, $id, $table);
	}

	function plgVmDeclarePluginParamsCustom($psType,$name,$id, &$data){
		return $this->declarePluginParams('custom', $name, $id, $data);
	}

	/**
	 * Custom triggers note by Max Milbers
	 */
	function plgVmOnDisplayEdit($virtuemart_custom_id,&$customPlugin){
		return $this->onDisplayEditBECustom($virtuemart_custom_id,$customPlugin);
	}

}

// No closing tag