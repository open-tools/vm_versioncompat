<?php
defined ('_JEXEC') or die();
/**
 *
 * @package    VirtueMart
 * @subpackage Plugins  - Elements
 * @author Reinhold Kainhofer
 * @link http://www.open-tools.net
 * @copyright   Copyright (C) 2014 Open Tools, Reinhold Kainhofer. All rights reserved.
 * Based on Joomla core files:
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 *
 * VirtueMart and this plugin are free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 *
 */
if (!class_exists('VmConfig'))  require(JPATH_VM_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'config.php');
if(!class_exists('VmModel'))    require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'vmmodel.php');
jimport('joomla.filesystem.folder');

/*
 * This class is used by VirtueMart Payment or Shipment Plugins
 * which uses JParameter
 * So It should be an extension of JElement
 * Those plugins cannot be configured througth the Plugin Manager anyway.
 */
class JElementCompatFolders extends JElement {

	/**
	 * Element name
	 *
	 * @access    protected
	 * @var        string
	 */
	var $_name = 'CompatFolders';

	protected function getGroups($node)
	{
		$groups = array(''=>JHtml::_('select.option', '', '---'));
	    foreach ($node->children() as $dir) {
    		if ($dir->name() != 'folder') continue;

			$directory = (string)$dir->data();
			if ($groupLabel = (string) $dir->attributes('label')) {
				$label = JText::_($groupLabel);
			} else {
				$label = $directory;
			}
			if (!JFolder::exists($directory))
                $directory = JPATH_ROOT . DS . $directory;
			if (!JFolder::exists($directory)) continue;

			// Initialize the group if necessary.
			if (!isset($groups[$label])) {
    			$groups[$label]['id'] = $label;
	       		$groups[$label]['text'] = $label;
	       		$groups[$label]['items'] = array();
			}

			// List all subfolders of the given directory:
			$folders = JFolder::folders($directory, (string)$dir->attributes('filter'));

			foreach ($folders as $option) {
	            $groups[$label]['items'][] = JHtml::_('select.option', (string)$dir->data().$option, $option);
			}
		}
		reset($groups);
		return $groups;
	}

	function fetchElement ($name, $value, &$node, $control_name) {
        $dirs = $this->getGroups($node);
        $class = ($node->attributes('class') ? 'class="' . $node->attributes('class') . '"' : '');
		return JHTML::_ ('select.groupedlist', $dirs, '' . $control_name . '[' . $name . ']',
		        array('id' => $control_name . $name, 'list.attr' => $class, 'list.select' => array($value)));
	}

}
