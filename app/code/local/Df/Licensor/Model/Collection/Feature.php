<?php
class Df_Licensor_Model_Collection_Feature extends Df_Varien_Data_Collection {
	/**
	 * @override
	 * @return string
	 */
	protected function getItemClass() {return Df_Licensor_Model_Feature::_CLASS;}
	const _CLASS = __CLASS__;

	/** @return Df_Licensor_Model_Collection_Feature */
	public static function i() {return new self;}
}