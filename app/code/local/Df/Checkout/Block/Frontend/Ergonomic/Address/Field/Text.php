<?php
class Df_Checkout_Block_Frontend_Ergonomic_Address_Field_Text
	extends Df_Checkout_Block_Frontend_Ergonomic_Address_Field {
	/**
	 * @override
	 * @return array
	 */
	protected function getCssClasses() {
		return array_merge(parent::getCssClasses(), array('input-text'));
	}

	/**
	 * @override
	 * @return string|null
	 */
	protected function getDefaultTemplate() {return self::DEFAULT_TEMPLATE;}

	const _CLASS = __CLASS__;
	const DEFAULT_TEMPLATE = 'df/checkout/ergonomic/address/field/text.phtml';
}