<?php
class Df_Tax_Model_Resource_Calculation_Rate extends Mage_Tax_Model_Mysql4_Calculation_Rate {
	/** @return Df_Tax_Model_Resource_Calculation_Rate */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}