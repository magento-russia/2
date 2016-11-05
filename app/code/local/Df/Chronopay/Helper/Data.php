<?php
class Df_Chronopay_Helper_Data extends Mage_Core_Helper_Abstract {
	/** @return Df_Chronopay_Helper_Cardholder_Name_Converter_Config */
	public function cartholderNameConversionConfig() {
		return Df_Chronopay_Helper_Cardholder_Name_Converter_Config::s();
	}

	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}