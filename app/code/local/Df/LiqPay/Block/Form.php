<?php
/** @method Df_LiqPay_Method method() */
class Df_LiqPay_Block_Form extends Df_Payment_Block_Form {
	/**
	 * @override
	 * @see Df_Core_Block_Template::defaultTemplate()
	 * @used-by Df_Core_Block_Template::getTemplate()
	 * @return string
	 */
	protected function defaultTemplate() {return 'df/liqpay/form.phtml';}
}