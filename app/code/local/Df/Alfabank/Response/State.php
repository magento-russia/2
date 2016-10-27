<?php
class Df_Alfabank_Response_State extends Df_Alfabank_Response {
	/** @return int */
	public function getAuthCode() {return $this->cfg(self::$P__AUTHCODE);}
	/** @return string */
	public function getCardholderName() {return $this->cfg(self::$P__CARDHOLDER_NAME);}
	/** @return string */
	public function getCardNumberMasked() {return $this->cfg(self::$P__PAN);}
	/** @return int */
	public function getCurrencyCode() {return $this->cfg(self::$P__CURRENCY_CODE);}
	/** @return int */
	public function getDepositAmount() {return $this->cfg(self::$P__DEPOSIT_AMOUNT);}
	/**
	 * @override
	 * @return array(int => string)
	 */
	protected function getErrorCodeMap() {
		/**
		 * 2015-02-08
		 * Ошибочно вместо операции «+» использовать здесь @see array_merge(),
		 * потому что @see array_merge() не сохраняет числовые ключи (перенумеровывает их).
		 * Обратите внимание, что операция «+» игнорирует те элементы второго массива,
		 * ключи которого присутствуют в первом массиве:
		 * «The keys from the first array will be preserved.
		 * If an array key exists in both arrays,
		 * then the element from the first array will be used
		 * and the matching key's element from the second array will be ignored.»
		 * http://php.net/manual/function.array-merge.php
		 */
		return
				array(2 => 'Заказ отклонен по причине ошибки в реквизитах платежа')
			+
				parent::getErrorCodeMap()
		;
	}
	/** @return string */
	public function getIpAddress() {return $this->cfg(self::$P__IP_ADDRESS);}
	/** @return string */
	public function getOrderIncrementId() {return $this->cfg(self::$P__ORDER_INCREMENT_ID);}
	/** @return int */
	public function getPaymentStatus() {return $this->cfg(self::$P__PAYMENT_STATUS);}
	/** @return string */
	public function getPaymentStatusMeaning() {
		return
			dfa(
				array(
					0 => 'Заказ зарегистрирован, но не оплачен'
					,1 => 'Проведена предавторизация суммы заказа'
					,2 => 'Проведена полная авторизация суммы заказа'
					,3 => 'Авторизация отменена'
					,4 => 'По транзакции была проведена операция возврата'
					,5 => 'Инициирована авторизация через ACS банка-эмитента'
					,6 => 'Авторизация отклонена'
				)
				,$this->getPaymentStatus()
				,'Неизвестно'
			)
		;
	}

	/** @return int */
	public function getPaymentAmount() {return $this->cfg(self::$P__AMOUNT);}

	/**
	 * @override
	 * @return array(string => string)
	 */
	public function getReportAsArray() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = array_filter(array(
				'Диагностическое сообщение' => $this->onFail($this->getErrorMessage())
				,'Состояние платежа' => $this->getPaymentStatusMeaning()
				,'Детали сбоя' => $this->onFail($this->getErrorCodeMeaning())
				,'Номер заказа' => $this->getOrderIncrementId()
				,'Имя владельца карты' => $this->getCardholderName()
				,'Номер карты' => $this->getCardNumberMasked()
				,'Размер платежа' => df_f2($this->getPaymentAmount() / 100)
				,'Код валюты' => $this->getCurrencyCode()
				,'Адрес IP плательщика' => $this->getIpAddress()
			));
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return bool
	 */
	public function isTransactionClosed() {
		// Помечаем транзакцию закрытой,
		// только если деньги с покупателя списаны.
		return 2 === $this->getPaymentStatus();
	}

	/**
	 * @override
	 * @return string
	 */
	public function getTransactionType() {
		return
			$this->isTransactionClosed()
			? Mage_Sales_Model_Order_Payment_Transaction::TYPE_PAYMENT
			: Mage_Sales_Model_Order_Payment_Transaction::TYPE_AUTH
		;
	}

	/**
	 * @override
	 * @return void
	 * @throws \Df\Payment\Exception\Response
	 */
	public function throwOnFailure() {
		parent::throwOnFailure();
		if (!in_array($this->getPaymentStatus(), array(1, 2, 5))) {
			$this->throwException('Заказ не был оплачен.');
		}
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getKey_ErrorCode() {return 'ErrorCode';}

	/**
	 * @override
	 * @return string
	 */
	protected function getKey_ErrorMessage() {return 'ErrorMessage';}

	/** @return string[] */
	protected function getKeysToSuppress() {return array('Состояние платежа');}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::$P__AMOUNT, DF_V_NAT0)
			->_prop(self::$P__AUTHCODE, DF_V_INT)
			->_prop(self::$P__CARDHOLDER_NAME, DF_V_STRING)
			->_prop(self::$P__CURRENCY_CODE, DF_V_NAT)
			->_prop(self::$P__DEPOSIT_AMOUNT, DF_V_NAT0)
			->_prop(self::$P__IP_ADDRESS, DF_V_STRING)
			->_prop(self::$P__ORDER_INCREMENT_ID, DF_V_STRING_NE)
			->_prop(self::$P__PAN, DF_V_STRING)
			->_prop(self::$P__PAYMENT_STATUS, DF_V_NAT0)
		;
	}
	/** @var int */
	private static $P__AMOUNT = 'Amount';
	/** @var string */
	private static $P__AUTHCODE = 'authCode';
	/** @var string */
	private static $P__CARDHOLDER_NAME = 'cardholderName';
	/** @var int */
	private static $P__CURRENCY_CODE = 'currency';
	/** @var int */
	private static $P__DEPOSIT_AMOUNT = 'depositAmount';
	/** @var string */
	private static $P__IP_ADDRESS = 'Ip';
	/** @var string */
	private static $P__ORDER_INCREMENT_ID = 'OrderNumber';
	/** @var string */
	private static $P__PAN = 'Pan';
	/** @var string */
	private static $P__PAYMENT_STATUS = 'OrderStatus';
	/**
	 * @used-by Df_Alfabank_Block_Info::getState()
	 * @return Df_Alfabank_Response_State
	 */
	public static function i() {return new self;}
}