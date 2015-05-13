<?php
class Df_Localization_Model_Translation_File_Collection extends Df_Varien_Data_Collection {
	/**
	 * @override
	 * @return string
	 */
	protected function getItemClass() {return Df_Localization_Model_Translation_File::_CLASS;}
	/** @return Df_Localization_Model_Translation_File_Collection */
	public static function i() {return new self;}
}