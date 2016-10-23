<?php
/** @method Df_IPay_Method method() */
class Df_IPay_Block_Form extends Df_Payment_Block_Form {
	/** @return array */
	public function getPaymentOptions() {
		return $this->method()->constManager()->availablePaymentMethodsAsCanonicalConfigArray();
	}

	/**
	 * @override
	 * @see Df_Core_Block_Template::defaultTemplate()
	 * @used-by Df_Core_Block_Template::getTemplate()
	 * @return string
	 */
	protected function defaultTemplate() {return 'df/ipay/form.phtml';}
}