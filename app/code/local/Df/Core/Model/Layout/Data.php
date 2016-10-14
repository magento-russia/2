<?php
class Df_Core_Model_Layout_Data extends Mage_Core_Model_Layout_Data {
	/**
	 * @override
	 * @return Df_Core_Model_Resource_Layout_Collection
	 */
	public function getResourceCollection() {return self::c();}

	/**
	 * @override
	 * @return Df_Core_Model_Resource_Layout
	 */
	protected function _getResource() {return Df_Core_Model_Resource_Layout::s();}

	/** @used-by Df_Core_Model_Resource_Layout_Collection::_construct() */
	const _C = __CLASS__;
	/** @return Df_Core_Model_Resource_Layout_Collection */
	public static function c() {return new Df_Core_Model_Resource_Layout_Collection;}
	/** @return Df_Core_Model_Layout_Data */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}