<?php
class Df_Cms_Model_ContentsMenu_Applicator_Collection extends Df_Varien_Data_Collection {
	/**
	 * @override
	 * @see Df_Varien_Data_Collection::itemClass()
	 * @used-by Df_Varien_Data_Collection::addItem()
	 * @return string
	 */
	protected function itemClass() {return Df_Cms_Model_ContentsMenu_Applicator::_C;}

	/** @return Df_Cms_Model_ContentsMenu_Applicator_Collection */
	public static function i() {return new self;}
}