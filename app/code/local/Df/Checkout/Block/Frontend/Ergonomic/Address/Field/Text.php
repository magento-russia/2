<?php
class Df_Checkout_Block_Frontend_Ergonomic_Address_Field_Text
	extends Df_Checkout_Block_Frontend_Ergonomic_Address_Field {
	/**
	 * @override
	 * @see Df_Checkout_Block_Frontend_Ergonomic_Address_Field::getCssClasses()
	 * @used-by Df_Checkout_Block_Frontend_Ergonomic_Address_Field::getCssClassesAsText()
	 * @return array
	 */
	protected function getCssClasses() {
		return array_merge(parent::getCssClasses(), array('input-text'));
	}

	/**
	 * @override
	 * @see Df_Core_Block_Template::defaultTemplate()
	 * @used-by Df_Core_Block_Template::getTemplate()
	 * @return string|null
	 */
	protected function defaultTemplate() {
		return 'df/checkout/ergonomic/address/field/text.phtml';
	}
}