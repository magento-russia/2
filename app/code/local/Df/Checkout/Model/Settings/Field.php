<?php
class Df_Checkout_Model_Settings_Field extends Df_Core_Model_Settings {
	/**
	 * @param string $addressType
	 * @return Df_Checkout_Model_Settings_Field_Applicability
	 */
	public function getApplicabilityByAddressType($addressType) {
		df_param_string($addressType, 0);
		if (!isset($this->{__METHOD__}[$addressType])) {
			$this->{__METHOD__}[$addressType] =
				Df_Checkout_Model_Settings_Field_Applicability::i($addressType)
			;
		}
		return $this->{__METHOD__}[$addressType];
	}

	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}