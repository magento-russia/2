<?php
class Df_Localization_Translation_File_Collection extends Df_Varien_Data_Collection {
	/**
	 * @override
	 * @see Df_Varien_Data_Collection::itemClass()
	 * @used-by Df_Varien_Data_Collection::addItem()
	 * @return string
	 */
	protected function itemClass() {return Df_Localization_Translation_File::class;}

	/** @return Df_Localization_Translation_File_Collection */
	public static function i() {return new self;}
}