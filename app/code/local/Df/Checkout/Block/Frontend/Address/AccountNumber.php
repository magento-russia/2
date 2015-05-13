<?php
class Df_Checkout_Block_Frontend_Address_AccountNumber
	extends Df_Checkout_Block_Frontend_Address_Element {
	/**
	 * @override
	 * @return string|null
	 */
	protected function getDefaultTemplate() {return self::DEFAULT_TEMPLATE;}

	const _CLASS = __CLASS__;
	const DEFAULT_TEMPLATE = 'df/checkout/address/accountNumber.phtml';
}