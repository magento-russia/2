<?php
class Df_Alfabank_Model_Request_Capture extends Df_Alfabank_Model_Request_Secondary {
	/** @return Df_Core_Model_Money */
	public function getAmount() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				Df_Core_Model_Money::i(
					rm_currency()->convertFromBaseToRoubles(
						$amount = $this->cfg(self::P__AMOUNT)
						,$store = $this->getOrderPayment()->getOrder()->getStore()
					)
				)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return array(string => string|int|float) */
	protected function getAdditionalParams() {
		return array('amount' => rm_round(100 * $this->getAmount()->getAsFixedFloat()));
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getGenericFailureMessageUniquePart() {
		return 'снятии ранее зарезервированных средств с карты покупателя';
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getResponseClass() {return Df_Alfabank_Model_Response_Capture::_CLASS;}

	/**
	 * @override
	 * @return string
	 */
	protected function getServiceName() {return 'deposit';}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__AMOUNT, self::V_FLOAT);
	}
	const _CLASS = __CLASS__;
	const P__AMOUNT = 'amount';
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Alfabank_Model_Request_Capture
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}


