<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2014 Open Tools, Reinhold Kainhofer. All rights reserved.
 * Based on Joomla core files:
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

jimport('joomla.filesystem.folder');
JFormHelper::loadFieldClass('groupedlist');

class JFormFieldCompatFolders extends JFormFieldGroupedList
{
	public $type = 'CompatFolders';

	protected function getGroups()
	{
		$groups = array(JHtml::_('select.option', '', '---'));
		foreach ($this->element->children() as $dir) {
			if ($dir->getName() != 'folder') continue;

			$directory = (string)$dir;
			if ($groupLabel = (string) $dir['label']) {
				$label = JText::_($groupLabel);
			} else {
				$label = $directory;
			}
			if (!JFolder::exists($directory))
                $directory = JPATH_ROOT . DS . $directory;
			if (!JFolder::exists($directory)) continue;

			// Initialize the group if necessary.
			if (!isset($groups[$label]))
				$groups[$label] = array();

			// List all subfolders of the given directory:
			$folders = JFolder::folders($directory, (string)$dir['filter']);

			foreach ($folders as $option) {
				// TODO: Make sure the $dir ends with a slash! (currently it appears to be always the case, but who knows)
				$groups[$label][] = JHtml::_('select.option', (string)$dir . $option, $option);
			}
		}
		reset($groups);
		return $groups;
	}
}
