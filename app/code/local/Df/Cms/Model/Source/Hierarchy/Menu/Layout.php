<?php
class Df_Cms_Model_Source_Hierarchy_Menu_Layout {
	/**
	 * Return options for displaying Hierarchy Menu
	 *
	 * @param bool $withDefault Include or not default value
	 * @return array
	 */
	public function toOptionArray($withDefault = false) {
		$options = array();
		if ($withDefault) {
		   $options[]= rm_option('', df_h()->cms()->__('Use Default'));
		}
		foreach (Df_Cms_Model_Hierarchy_Config::s()->getContextMenuLayouts() as $code => $info) {
			/** @var string $code */
			/** @var Varien_Object $info */
			$options[]= rm_option($code, $info->getData('label'));
		}
		return $options;
	}
}