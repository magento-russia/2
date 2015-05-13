<?php
abstract class Df_Masterbank_Model_Response extends Df_Payment_Model_Response {
	/** @return string */
	public function getAuthCode() {return $this->cfg('AUTHCODE');}

	/** @return string */
	public function getCardNumberMasked() {return $this->cfg(self::$P__PAN);}

	/** @return int */
	public function getOperationCode() {return $this->cfg(self::$P__TRTYPE);}

	/** @return string */
	public function getOperationCodeMeaning() {
		return
			df_a(
				array(
					0 => 'авторизация'
					, 21 => 'завершение расчёта'
					, 24 => 'возврат'
				)
				,$this->getOperationCode()
			)
		;
	}

	/** @return string */
	public function getOperationCodeExternal() {return $this->cfg(self::$P__INT_REF);}

	/** @return string */
	public function getRequestExternalId() {return $this->cfg(self::$P__RRN);}

	/** @return int */
	public function getResponseCode() {return $this->cfg(self::$P__RESULT);}

	/** @return string */
	public function getResponseCodeMeaning() {
		return
			df_a(
				array(
					0 => 'платёж одобрен'
					, 1 => 'попытка повторной оплаты'
					, 2 => 'платёж отклонён'
					, 3 => 'на стороне Мастер-Банка произошёл сбой'
				)
				,$this->getResponseCode()
			)
		;
	}

	/** @return Zend_Date */
	public function getTime() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df()->date()->fromTimestamp14($this->getTimestamp(), 'GMT');
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function getTimestamp() {return $this->cfg('TIMESTAMP');}

	/**
	 * @override
	 * @return string
	 */
	public function getTransactionType() {return Mage_Sales_Model_Order_Payment_Transaction::TYPE_PAYMENT;}

	/**
	 * @override
	 * @return array(string => string)
	 */
	public function getReportAsArray() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_clean(array(
				'Диагностическое сообщение' => $this->onFail($this->getErrorMessage())
				,'Состояние платежа' => $this->onSucc($this->getOperationCodeMeaning())
				,'Дата и время платежа' => df_dts($this->getTime(), 'dd.MM.y HH:mm:ss')
			));
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return bool
	 */
	protected function isSuccessful() {return 0 === $this->getResponseCode();}
	/**
	 * @override
	 * @return bool
	 */
	public function isTransactionClosed() {return false;}
	/**
	 * @override
	 * @return string
	 */
	protected function getErrorMessage() {return $this->getResponseCodeMeaning();}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::$P__INT_REF, self::V_STRING)
			->_prop(self::$P__PAN, self::V_STRING)
			->_prop(self::$P__RESULT, self::V_NAT0)
			->_prop(self::$P__RRN, self::V_STRING)
			->_prop(self::$P__TRTYPE, self::V_NAT0)
		;
	}
	const _CLASS = __CLASS__;
	/** @var string */
	private static $P__INT_REF = 'INT_REF';
	/** @var string */
	private static $P__PAN = 'PAN';
	/** @var string */
	private static $P__RESULT = 'RESULT';
	/** @var string */
	private static $P__RRN = 'RRN';
	/** @var string */
	private static $P__TRTYPE = 'TRTYPE';
}