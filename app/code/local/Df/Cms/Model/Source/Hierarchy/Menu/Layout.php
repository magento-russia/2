<?php
class Df_Cms_Model_Source_Hierarchy_Menu_Layout {
	/**
	 * Return options for displaying Hierarchy Menu
	 *
	 * @param bool $withDefault Include or not default value
	 * @return array
	 */
	public function toOptionArray($withDefault = false)
	{
		$options = array();
		if ($withDefault) {
		   $options[]= array('label' => df_h()->cms()->__('Use Default'), 'value' => '');
		}

		foreach (Df_Cms_Model_Hierarchy_Config::s()->getContextMenuLayouts() as $code => $info) {
			$options[]= array(
				'label' => $info->getLabel(),'value' => $code
			);
		}
		return $options;
	}
}