<?php
namespace Df\LiqPay\Block;
/** @method \Df\LiqPay\Method method() */
class Form extends \Df\Payment\Block\Form {
	/**
	 * @override
	 * @see \Df_Core_Block_Template::defaultTemplate()
	 * @used-by \Df_Core_Block_Template::getTemplate()
	 * @return string
	 */
	protected function defaultTemplate() {return 'df/liqpay/form.phtml';}
}