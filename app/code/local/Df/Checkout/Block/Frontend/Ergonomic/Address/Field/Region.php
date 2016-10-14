<?php
class Df_Checkout_Block_Frontend_Ergonomic_Address_Field_Region
	extends Df_Checkout_Block_Frontend_Ergonomic_Address_Field {
	/** @return Df_Checkout_Block_Frontend_Ergonomic_Address_Field_Dropdown */
	public function getControlDropdown() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = new Df_Checkout_Block_Frontend_Ergonomic_Address_Field_Dropdown(
				$this->getData()
			);
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Checkout_Block_Frontend_Ergonomic_Address_Field_Region_Text */
	public function getControlText() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = new Df_Checkout_Block_Frontend_Ergonomic_Address_Field_Region_Text(
				$this->getData()
			);
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @see Df_Core_Block_Template::defaultTemplate()
	 * @used-by Df_Core_Block_Template::getTemplate()
	 * @return string
	 */
	protected function defaultTemplate() {return 'df/checkout/ergonomic/address/field/region.phtml';}
}