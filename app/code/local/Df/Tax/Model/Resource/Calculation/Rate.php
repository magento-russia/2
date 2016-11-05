<?php
class Df_Tax_Model_Resource_Calculation_Rate extends Mage_Tax_Model_Resource_Calculation_Rate {
	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}