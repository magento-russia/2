<?php
/** @method Df_Qiwi_Model_Payment getMethod() */
class Df_Qiwi_Block_Form extends Df_Payment_Block_Form {
	/** @return string */
	public function getQiwiCustomerPhoneNetworkCode() {return dfc($this, function() {return
		strval(substr($this->getQiwiCustomerPhone(), 0, 3))
	;});}

	/** @return string */
	public function getQiwiCustomerPhoneSuffix() {return dfc($this, function() {return
		strval(substr($this->getQiwiCustomerPhone(), 3))
	;});}

	/** @return string */
	public function getQiwiCustomerPhoneNetworkCodeCssClassesAsString() {return df_cc_s([
		'input-text'
		,'df-phone-network-code'
		,'required-entry'
		,'validate-digits'
		,'validate-length'
		,'minimum-length-3'
		,'maximum-length-3'
	]);}

	/** @return string */
	public function getQiwiCustomerPhoneSuffixCssClassesAsString() {return df_cc_s([
		'input-text'
		,'df-phone-suffix'
		,'required-entry'
		,'validate-digits'
		,'validate-length'
		,'minimum-length-7'
		,'maximum-length-7'
	]);}

	/**
	 * @override
	 * @see Df_Core_Block_Template::defaultTemplate()
	 * @used-by Df_Core_Block_Template::getTemplate()
	 * @return string
	 */
	protected function defaultTemplate() {return 'df/qiwi/form.phtml';}

	/** @return \Df\Core\Format\MobilePhoneNumber */
	private function getBillingAddressPhone() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = \Df\Core\Format\MobilePhoneNumber::fromQuoteAddress(
				df_quote_address_billing()
			);
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getQiwiCustomerPhone() {
		/** @var string $result */
		$result = $this->getMethod()->getQiwiCustomerPhone();
		if (!$result) {
			if ($this->getBillingAddressPhone()->isValid()) {
				$result = $this->getBillingAddressPhone()->getOnlyDigitsWithoutCallingCode();
			}
			else if ($this->getShippingAddressPhone()->isValid()) {
				$result = $this->getShippingAddressPhone()->getOnlyDigitsWithoutCallingCode();
			}
			else {
				$result = '';
			}
		}
		df_result_string($result);
		return $result;
	}

	/** @return \Df\Core\Format\MobilePhoneNumber */
	private function getShippingAddressPhone() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = \Df\Core\Format\MobilePhoneNumber::i(
				df_quote_address_shipping()->getTelephone()
			);
		};
		return $this->{__METHOD__};
	}


	const T_FIELD_LABEL__QIWI_CUSTOMER_ID = 'Номер телефона';
	const T_FIELD_LABEL__QIWI_CUSTOMER_PHONE__NETWORK_CODE = 'Код:';
	const T_FIELD_LABEL__QIWI_CUSTOMER_PHONE__SUFFIX = 'Номер:';
}