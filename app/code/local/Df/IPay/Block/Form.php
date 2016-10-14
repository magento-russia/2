<?php
/** @method Df_IPay_Model_Payment getMethod() */
class Df_IPay_Block_Form extends Df_Payment_Block_Form {
	/** @return array */
	public function getPaymentOptions() {
		return $this->getMethod()->constManager()->getAvailablePaymentMethodsAsCanonicalConfigArray();
	}

	/**
	 * @override
	 * @see Df_Core_Block_Template::defaultTemplate()
	 * @used-by Df_Core_Block_Template::getTemplate()
	 * @return string
	 */
	protected function defaultTemplate() {return 'df/ipay/form.phtml';}
}