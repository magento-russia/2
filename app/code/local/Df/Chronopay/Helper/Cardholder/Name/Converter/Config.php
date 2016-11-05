<?php
class Df_Chronopay_Helper_Cardholder_Name_Converter_Config extends Mage_Core_Helper_Abstract {
	/** @return array */
	public function getConversionTable() {
		return
			array(
				"Æ" => "AE"
				, "Ø" => "OE"
				, "Å" => "AA"
			)
		;
	}

	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}