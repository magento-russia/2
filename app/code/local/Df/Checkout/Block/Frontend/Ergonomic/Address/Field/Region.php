<?php
class Df_Checkout_Block_Frontend_Ergonomic_Address_Field_Region
	extends Df_Checkout_Block_Frontend_Ergonomic_Address_Field {
	/** @return Df_Checkout_Block_Frontend_Ergonomic_Address_Field_Dropdown */
	public function getControlDropdown() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				Df_Checkout_Block_Frontend_Ergonomic_Address_Field_Dropdown::i($this->getData())
			;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Checkout_Block_Frontend_Ergonomic_Address_Field_Region_Text */
	public function getControlText() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				Df_Checkout_Block_Frontend_Ergonomic_Address_Field_Region_Text::i($this->getData())
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return string|null
	 */
	protected function getDefaultTemplate() {return 'df/checkout/ergonomic/address/field/region.phtml';}

	const _CLASS = __CLASS__;
}