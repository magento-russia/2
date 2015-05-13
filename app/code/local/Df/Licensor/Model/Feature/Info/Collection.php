<?php
class Df_Licensor_Model_Feature_Info_Collection extends Df_Varien_Data_Collection {
	/**
	 * @override
	 * @return string
	 */
	protected function getItemClass() {return Df_Licensor_Model_Feature_Info::_CLASS;}
	const _CLASS = __CLASS__;
	/** @return Df_Licensor_Model_Feature_Info_Collection */
	public static function i() {return new self;}
}