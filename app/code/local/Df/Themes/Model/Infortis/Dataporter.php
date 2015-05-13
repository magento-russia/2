<?php
class Df_Themes_Model_Infortis_Dataporter {
	/**
	 * @param array(array(string => string)) $options
	 * @return array(array(string => string))
	 */
	public static function translateOptions(array $options) {
		/** @var Infortis_Dataporter_Helper_Data $helper */
		$helper = Mage::helper('dataporter');
		foreach ($options as &$option) {
			/** @var array(string => string) */
			$option['label'] = $helper->__($option['label']);
		}
		return $options;
	}
}