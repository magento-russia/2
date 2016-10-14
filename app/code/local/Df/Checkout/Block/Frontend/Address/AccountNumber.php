<?php
class Df_Checkout_Block_Frontend_Address_AccountNumber
	extends Df_Checkout_Block_Frontend_Address_Element {
	/**
	 * @override
	 * @see Df_Core_Block_Template::defaultTemplate()
	 * @used-by Df_Core_Block_Template::getTemplate()
	 * @return string
	 */
	protected function defaultTemplate() {return 'df/checkout/address/accountNumber.phtml';}
}