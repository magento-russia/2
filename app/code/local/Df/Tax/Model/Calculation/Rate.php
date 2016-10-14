<?php
class Df_Tax_Model_Calculation_Rate extends Mage_Tax_Model_Calculation_Rate {
	/**
	 * @override
	 * @return Df_Tax_Model_Resource_Calculation_Rate_Collection
	 */
	public function getResourceCollection() {return self::c();}

	/**
	 * @override
	 * @return Df_Tax_Model_Resource_Calculation_Rate
	 */
	protected function _getResource() {return Df_Tax_Model_Resource_Calculation_Rate::s();}

	/** @used-by Df_Tax_Model_Resource_Calculation_Rate_Collection::_construct() */
	const _C = __CLASS__;
	/**
	 * @static
	 * @return Df_Tax_Model_Resource_Calculation_Rate_Collection
	 */
	public static function c() {return new Df_Tax_Model_Resource_Calculation_Rate_Collection;}
	/** @return Df_Tax_Model_Calculation_Rate */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}