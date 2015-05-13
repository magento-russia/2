<?php
class Df_Cms_Model_ContentsMenu_Applicator_Collection extends Df_Varien_Data_Collection {
	/**
	 * @override
	 * @return string
	 */
	protected function getItemClass() {return Df_Cms_Model_ContentsMenu_Applicator::_CLASS;}
	const _CLASS = __CLASS__;
	/** @return Df_Cms_Model_ContentsMenu_Applicator_Collection */
	public static function i() {return new self;}
}