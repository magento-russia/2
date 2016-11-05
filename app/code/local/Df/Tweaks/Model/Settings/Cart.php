<?php
class Df_Tweaks_Model_Settings_Cart extends Df_Core_Model_Settings {
	/** @return boolean */
	public function removeCrosssellBlock() {return $this->getYesNo('crosssell_block');}
	/** @return boolean */
	public function removeDiscountCodesBlock() {return $this->getYesNo('discount_codes_block');}
	/** @return boolean */
	public function removeShippingAndTaxEstimation() {return $this->getYesNo('shipping_and_tax_estimation');}
	/**
	 * @override
	 * @return string
	 */
	protected function getKeyPrefix() {return 'df_tweaks/checkout_cart/remove_';}
	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}