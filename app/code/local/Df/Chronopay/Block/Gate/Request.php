<?php
class Df_Chronopay_Block_Gate_Request extends Df_Core_Block_Template_NoCache {
	/** @return double */
	public function getBaseGrandTotal() {
		return $this->info()->getOrder()->getBaseGrandTotal();
	}

	/** @return Df_Chronopay_Model_Gate_Buyer */
	public function getBuyer() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Chronopay_Model_Gate_Buyer::i($this->info());
		}
		return $this->{__METHOD__};
	}
	/** @return Df_Chronopay_Model_Gate_Card */
	public function getCard() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Chronopay_Model_Gate_Card::i($this->info());
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function getHash() {
		return md5(implode('-', array(
			rm_decrypt($this->getConfigParam('shared_sec'))
			,$this->getOperationCode()
			,$this->getProductId()
		)));
	}

	/** @return int */
	public function getOperationCode() {return 1;}

	/** @return string[] */
	public function getOptions() {
		if (!isset($this->{__METHOD__})) {
			/** @uses getConfigParam() */
			$this->{__METHOD__} =
				array_keys(array_filter(array_map(array($this, 'getConfigParam'), array(
					'skipClientCallback' => 'skip_client_callback'
					,'skipCustomerEmail' => 'skip_customer_email'
					,'skipRebill' => 'skip_rebill'
				))))
			;
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
				rm_currency_h()->getBase()->convert(
					$this->getBaseGrandTotal()
					,$this->method()->getChronopayCurrency()
				)
			;
			/** @var string $result */
			$result = number_format((float)$convertedPrice,2 ,'.', '');
		}
		catch (Exception $e) {
			df_handle_entry_point_exception($e, true);
		}
		return $result;
	}

	/** @return string */
	public function getProductId() {return $this->getConfigParam('product_id');}

	/**
	 * @override
	 * @see Df_Core_Block_Template::defaultTemplate()
	 * @used-by Df_Core_Block_Template::getTemplate()
	 * @return string
	 */
	protected function defaultTemplate() {return 'df/chronopay/gate/request.xml';}

	/**
	 * @param string $key
	 * @param string|null $default [optional]
	 * @return string|null
	 */
	private function getConfigParam($key, $default = null) {
		/** @var string|null $value */
		$value = $this->method()->getConfigData($key);
		return $value ? $value : $default;
	}

	/** @return Mage_Payment_Model_Info */
	private function info() {return $this[self::$P__INFO];}

	/** @return Df_Chronopay_Model_Gate */
	private function method() {return $this[self::$P__METHOD];}
	/** @var string */
	private static $P__INFO = 'info';
	/** @var string */
	private static $P__METHOD = 'method';
	/**
	 * @used-by Df_Chronopay_Model_Gate::sendPurchaseRequest()
	 * @param Df_Chronopay_Model_Gate $method
	 * @param Mage_Payment_Model_Info $info
	 * @return string
	 */
	public static function r(Df_Chronopay_Model_Gate $method, Mage_Payment_Model_Info $info) {
		return df_render(__CLASS__, array(self::$P__METHOD => $method, self::$P__INFO => $info));
	}
}