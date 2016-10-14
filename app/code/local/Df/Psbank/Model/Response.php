<?php
class Df_Psbank_Model_Response extends Df_Payment_Model_Response {
	/**
	 * Назначение: сумма операции.
	 * Присутствует во всех ответах.
	 * Формат данных: числовой с десятичной точкой.
	 * Длина данных: 1-11.
	 * @used-by getReportAsArray()
	 * @used-by Df_Psbank_Model_Request_Secondary::getParamsForSignature()
	 * @return Df_Core_Model_Money
	 */
	public function amount() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_money($this->cfg('AMOUNT'));
		}
		return $this->{__METHOD__};
	}

	/**
	 * Назначение: код авторизации (буквенно-цифровой код, выдаваемый банком, выпустившим карту).
	 * Присутствует только в ответах на запросы оплаты и предавторизации.
	 * Формат данных: символьный.
	 * Длина данных: 6-32.
	 * @return string
	 */
	public function getAuthCode() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->cfg('AUTHCODE');
			df_result_string_not_empty($this->{__METHOD__});
			df_result_between(strlen($this->{__METHOD__}), 6, 32);
		}
		return $this->{__METHOD__};
	}

	/**
	 * Назначение: URL для возврата на сайт Торговой точки после проведения операции.
	 * Присутствует во всех ответах.
	 * Формат данных: символьный.
	 * Длина данных: 1-250.
	 * @return string
	 */
	public function getBackRef() {return $this->cfg('BACKREF');}

	/**
	 * Назначение: имя держателя карты.
	 * Присутствует только в ответах на запросы оплаты и предавторизации
	 * (документация ошибочно говорит, что присутствует во всех ответах).
	 * Формат данных: символьный.
	 * Длина данных: 1-250.
	 * @return string
	 */
	public function getCardHolderName() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->cfg('NAME');
			df_result_string_not_empty($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/**
	 * Назначение: маскированный номер карты.
	 * Присутствует только в ответах на запросы оплаты и предавторизации
	 * (документация ошибочно говорит, что присутствует во всех ответах).
	 * Формат данных: символьный.
	 * Длина данных: 1-250.
	 * @return string
	 */
	public function getCardNumberMasked() {return $this->cfg('CARD');}

	/**
	 * Назначение: код ответа на попытку проведения операции.
	 * Присутствует во всех ответах.
	 * В документации написано, что формат данных — символьный, и длина — 1-2 символа.
	 * Результат может быть отрицательным числом, например, «-17».
	 * Может показаться, что код ответа всегда является целым числом,
	 * однако на практике встречал код ответа «00».
	 * http://magento-forum.ru/topic/4598/
	 * @return string
	 */
	public function getCode() {return $this->cfg(self::$P__RC);}

	/**
	 * Назначение: расшифровка кода ответа на попытку проведения операции.
	 * Присутствует во всех ответах.
	 * Формат данных: символьный.
	 * Длина данных: 1-250.
	 * @return string
	 */
	public function getCodeMeaning() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->cfg('RCTEXT');
			df_result_string_not_empty($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/**
	 * Назначение: валюта операции.
	 * Присутствует во всех ответах.
	 * Формат данных: символьный.
	 * Длина данных: 3.
	 * @return Df_Directory_Model_Currency
	 */
	public function getCurrency() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $currencyCode */
			$currencyCode = $this->cfg('CURRENCY');
			// Платёжный шлюз Промсвязьбанка работает только с рублём
			df_assert(Df_Directory_Model_Currency::RUB === $currencyCode);
			$this->{__METHOD__} = Df_Directory_Model_Currency::ld($currencyCode);
		}
		return $this->{__METHOD__};
	}

	/**
	 * Назначение: описание платежа.
	 * Присутствует только в ответах на запросы оплаты и приедавторизации.
	 * Формат данных: символьный.
	 * Длина данных: 0-50.
	 * @return string
	 */
	public function getDescription() {return $this->cfg(self::$P__DESC);}

	/**
	 * Назначение: адрес электронной почты интернет-магазина.
	 * Присутствует во всех ответах.
	 * Формат данных: символьный.
	 * Длина данных: 3-80.
	 * @return string
	 */
	public function getEmail() {return $this->cfg(self::$P__EMAIL);}

	/**
	 * Назначение: cлучайное число в шестнадцатеричном формате.
	 * Присутствует во всех ответах.
	 * Формат данных: символьный.
	 * Длина данных: 16-32.
	 * @return string
	 */
	public function getNonce() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->cfg('NONCE');
			df_result_string_not_empty($this->{__METHOD__});
			df_assert_between(strlen($this->{__METHOD__}), 16, 32);
		}
		return $this->{__METHOD__};
	}

	/**
	 * Назначение: идентификатор операции в платёжном шлюзе.
	 * Присутствует во всех ответах.
	 * Формат данных: символьный.
	 * Длина данных: 1-32.
	 * @used-by Df_Psbank_Model_Request_Secondary::getPaymentExternalId()
	 * @return string
	 */
	public function getOperationExternalId() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->cfg('INT_REF');
			df_result_string_not_empty($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/**
	 * Назначение: внутренний идентификатор заказа в интернет-магазине.
	 * Присутствует во всех ответах.
	 * Формат данных: числовой.
	 * Длина данных: 6-20.
	 * @return string
	 */
	public function getOrderIncrementId() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->cfg('ORDER');
			df_result_string_not_empty($this->{__METHOD__});
			df_assert(ctype_digit($this->{__METHOD__}));
			df_assert_between(strlen($this->{__METHOD__}), 6, 20);
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return array(string => string)
	 */
	public function getReportAsArray() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_clean(array(
				'Тип транзакции' => $this->getTransactionName()
				,'Состояние операции' => sprintf('%s (%s)', $this->getStatusMeaning(), $this->getCodeMeaning())
				,'Код состояния операции' => $this->getCode()
				,'Дата и время операции' => df_dts($this->getTime(), 'dd.MM.y HH:mm:ss')
				,'Сумма операции' => $this->amount()->getAsString()
				,'Валюта операции' => $this->getCurrency()->getName()
				,'Номер заказа' => $this->getOrderIncrementId()
				,'Номер терминала' => $this->getTerminalId()
			));
			if ($this->isSuccessful()) {
				$this->{__METHOD__} = array_merge($this->{__METHOD__}, df_clean(array(
					'Идентификатор операции в платёжном шлюзе' => $this->getOperationExternalId()
					,'Номер запроса на списание средств с карты' => $this->getRetrievalReferenceNumber()
				)));
			}
			if ($this->isPrimary()) {
				if ($this->isSuccessful()) {
					$this->{__METHOD__} = array_merge($this->{__METHOD__}, df_clean(array(
						'Номер банковской карты' => $this->getCardNumberMasked()
						,'Держатель банковской карты' => $this->getCardHolderName()
						,'Код авторизации' => $this->getAuthCode()
					)));
				}
			}
		}
		return $this->{__METHOD__};
	}

	/**
	 * Назначение: идентификатор запроса на списание средств с карты.
	 * Присутствует во всех ответах.
	 * Формат данных: числовой.
	 * Длина данных: 12.
	 * @return string
	 */
	public function getRetrievalReferenceNumber() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->cfg('RRN');
			df_result_string_not_empty($this->{__METHOD__});
			df_assert(ctype_digit($this->{__METHOD__}));
			df_assert_eq(12, strlen($this->{__METHOD__}));
		}
		return $this->{__METHOD__};
	}

	/**
	 * Назначение: название интернет-магазина.
	 * Присутствует только в ответах на запросы оплаты и приедавторизации.
	 * Формат данных: символьный.
	 * Длина данных: 0-50.
	 * @return string
	 */
	public function getShopName() {return $this->cfg(self::$P__MERCH_NAME);}

	/**
	 * Назначение: подпись ответа.
	 * Присутствует во всех ответах.
	 * Формат данных: символьный.
	 * Длина данных: 40.
	 * @return string
	 */
	public function getSignature() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->cfg('P_SIGN');
			df_result_string_not_empty($this->{__METHOD__});
			df_assert_eq(40, strlen($this->{__METHOD__}));
		}
		return $this->{__METHOD__};
	}

	/**
	 * Назначение: результат обработки запроса на операцию.
	 * 0 — операция успешно завершена
	 * 1 — запрос идентифицирован как повторный
	 * 2 — запрос отклонен Банком
	 * 3 — запрос отклонен Платёжным шлюзом
	 * Присутствует во всех ответах.
	 * Формат данных: числовой.
	 * Длина данных: 1.
	 * @return int
	 */
	public function getStatus() {
		if (!isset($this->{__METHOD__})) {
			/** @var int|string $result */
			$result = $this->cfg('RESULT');
			df_assert(!is_null($result));
			df_assert(ctype_digit($result));
			$result = rm_nat0($result);
			df_result_between($result, 0, 3);
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function getStatusMeaning() {
		return dfa(
			array(
				0 => 'операция успешно завершена'
				, 1 => 'запрос идентифицирован как повторный'
				, 2 => 'запрос отклонен банком'
				, 3 => 'запрос отклонен платёжным шлюзом'
			)
			,$this->getStatus()
		);
	}

	/**
	 * Назначение: идентификатор виртуального терминала торговой точки.
	 * Присутствует во всех ответах.
	 * Формат данных: числовой.
	 * Длина данных: 8.
	 * @return int
	 */
	public function getTerminalId() {
		if (!isset($this->{__METHOD__})) {
			/** @var int|string $result */
			$result = $this->cfg('TERMINAL');
			df_assert_eq(8, strlen($result));
			df_assert(ctype_digit($result));
			$this->{__METHOD__} = rm_nat0($result);
		}
		return $this->{__METHOD__};
	}

	/**
	 * Назначение: время операции.
	 * @return Zend_Date
	 */
	public function getTime() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df()->date()->fromTimestamp14($this->getTimestamp(), 'UTC');
		}
		return $this->{__METHOD__};
	}

	/**
	 * Назначение: время операции в формате «20131115153657» и в часовом поясе UTC.
	 * Присутствует во всех ответах.
	 * Формат данных: числовой.
	 * Длина данных: 14.
	 * @return string
	 */
	public function getTimestamp() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->cfg('TIMESTAMP');
			df_result_string_not_empty($this->{__METHOD__});
			df_assert(ctype_digit($this->{__METHOD__}));
			df_assert_eq(14, strlen($this->{__METHOD__}));
		}
		return $this->{__METHOD__};
	}

	/** @return int */
	public function getTransactionCode() {
		if (!isset($this->{__METHOD__})) {
			/** @var string|int $result */
			$result = $this->cfg('TRTYPE');
			df_result_string_not_empty($result);
			df_assert(ctype_digit($result));
			df_result_between(strlen($result), 1, 2);
			$this->{__METHOD__} = rm_nat0($result);
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return string
	 */
	public function getTransactionType() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				$this->getRmTransactionType()
				? $this->getRmTransactionType()
				: dfa(array(
					1 => Mage_Sales_Model_Order_Payment_Transaction::TYPE_PAYMENT
					,0 => Mage_Sales_Model_Order_Payment_Transaction::TYPE_AUTH
					,21 => Mage_Sales_Model_Order_Payment_Transaction::TYPE_CAPTURE
					,22 => Mage_Sales_Model_Order_Payment_Transaction::TYPE_VOID
				), $this->getTransactionCode());
		}
		return $this->{__METHOD__};
	}

	/**
	 * @used-by Df_Psbank_Model_Action_CustomerReturn::getRedirect()
	 * @override
	 * @return bool
	 */
	public function isSuccessful() {return 0 === $this->getStatus();}

	/**
	 * @override
	 * @return bool
	 */
	public function isTransactionClosed() {
		return Mage_Sales_Model_Order_Payment_Transaction::TYPE_AUTH !== $this->getTransactionType();
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getIdInPaymentInfo() {
		return implode('::', array(parent::getIdInPaymentInfo(), $this->getTransactionType()));
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getErrorMessage() {return $this->getStatusMeaning();}

	/** @return string|null */
	private function getRmTransactionType() {return $this->cfg(self::P__RM_TRANSACTION_TYPE);}

	/** @return bool */
	private function isPrimary() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = in_array($this->getTransactionCode(), array(0, 1));
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::$P__DESC, RM_V_STRING)
			->_prop(self::$P__EMAIL, RM_V_STRING)
			->_prop(self::$P__MERCH_NAME, RM_V_STRING)
			->_prop(self::$P__RC, RM_V_STRING)
			->_prop(self::P__RM_TRANSACTION_TYPE, RM_V_STRING, false)
		;
	}
	const _C = __CLASS__;
	const P__RM_TRANSACTION_TYPE = 'rm_transaction_type';
	/** @var string */
	private static $P__DESC = 'DESC';
	/** @var string */
	private static $P__EMAIL = 'EMAIL';
	/** @var string */
	private static $P__MERCH_NAME = 'MERCH_NAME';
	/** @var string */
	private static $P__RC = 'RC';
	/**
	 * @static
	 * @param string|mixed[] $parameters [optional]
	 * @return Df_Psbank_Model_Response
	 */
	public static function i($parameters = array()) {
		return new self(
			is_array($parameters) ? $parameters : array(self::P__RM_TRANSACTION_TYPE => $parameters)
		);
	}
}