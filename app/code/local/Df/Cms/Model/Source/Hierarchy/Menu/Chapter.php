<?php
class Df_Cms_Model_Source_Hierarchy_Menu_Chapter {
	/**
	 * Return options for Chapter/Section meta links
	 * @return array
	 */
	public function toOptionArray()
	{
		$helper = df_h()->cms();
		$options = array(
			array('label' => $helper->__('No'), 'value' => ''),array('label' => $helper->__('Chapter'), 'value' => 'chapter'),array('label' => $helper->__('Section'), 'value' => 'section'),array('label' => $helper->__('Both'), 'value' => 'both'),);
		return $options;
	}
}