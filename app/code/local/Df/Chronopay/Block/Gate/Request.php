<?php
class Df_Chronopay_Block_Gate_Request extends Df_Core_Block_Template_NoCache {
	/** @return double */
	public function getBaseGrandTotal() {
		return $this->getPaymentInfo()->getOrder()->getBaseGrandTotal();
	}

	/** @return Df_Chronopay_Model_Gate_Buyer */
	public function getBuyer() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Chronopay_Model_Gate_Buyer::i($this->getPaymentInfo());
		}
		return $this->{__METHOD__};
	}
	/** @return Df_Chronopay_Model_Gate_Card */
	public function getCard() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Chronopay_Model_Gate_Card::i($this->getPaymentInfo());
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function getHash() {
		return md5(implode('-', array(
			df_mage()->coreHelper()->decrypt($this->getConfigParam('shared_sec'))
			,$this->getOperationCode()
			,$this->getProductId()
		)));
	}

	/** @return int */
	public function getOperationCode() {return 1;}

	/** @return string[] */
	public function getOptions() {
		if (!isset($this->{__METHOD__})) {
			$options =
				array(
					'skip_client_callback' => 'skipClientCallback'
					,'skip_customer_email' => 'skipCustomerEmail'
					,'skip_rebill' => 'skipRebill'
				)
			;
			$this->{__METHOD__} = array();
			foreach ($options as $key => $tag) {
				if ($this->getConfigParam($key)) {
					$this->{__METHOD__}[]= $tag;
				}
			}
		}
		return $this->{__METHOD__};
	}

	/**
	 * @return string
	 * @throws Exception
	 */
	public function getPrice() {
		/** @var string $result */
		$result = null;
		try {
			$convertedPrice =
				rm_currency()->getBase()->convert(
					$this->getBaseGrandTotal()
					,$this->getPaymentController()->getChronopayCurrency()
				)
			;
			/** @var string $result */
			$result = number_format((float)$convertedPrice,2 ,'.', '');
		}
		catch(Exception $e) {
			df_handle_entry_point_exception($e, true);
		}
		return $result;
	}

	/** @return string */
	public function getProductId() {return $this->getConfigParam('product_id');}

	/**
	 * @param string $key
	 * @param string|null $default[optional]
	 * @return string|null
	 */
	private function getConfigParam($key, $default = null) {
		/** @var string|null $value */
		$value = $this->getPaymentController()->getConfigData($key);
		return $value ? $value : $default;
	}

	/** @return Df_Chronopay_Model_Gate */
	private function getPaymentController() {return $this->_getData('paymentController');}

	/** @return Mage_Payment_Model_Info */
	private function getPaymentInfo() {return $this->_getData('paymentInfo');}

	const _CLASS = __CLASS__;
	const P__PAYMENT_CONTROLLER = 'paymentController';
	const P__PAYMENT_INFO = 'paymentInfo';
	/**
	 * @param Df_Chronopay_Model_Gate $paymentController
	 * @param Mage_Payment_Model_Info $paymentInfo
	 * @return Df_Chronopay_Block_Gate_Request
	 */
	public static function i(
		Df_Chronopay_Model_Gate $paymentController, Mage_Payment_Model_Info $paymentInfo
	) {
		return df_block(new self(array(
			self::P__PAYMENT_CONTROLLER => $paymentController
			, self::P__PAYMENT_INFO => $paymentInfo
		)));
	}
}