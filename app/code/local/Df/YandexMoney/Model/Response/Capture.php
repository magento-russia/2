<?php
class Df_YandexMoney_Model_Response_Capture extends Df_YandexMoney_Model_Response {
	/**
	 * Параметры авторизации по технологии 3D-Secure в формате коллекции имя-значение.
	 * Поле присутствует если для завершения транзакции с использованием банковской карты
	 * требуется авторизация по 3D-Secure.
	 * Пример:
		"acs_params": {
			 "MD":"723613-7431F11492F4F2D0",
			 "PaReq":"eJxVUl1T2zAQ/CsZv8f6tCR7LmLSGiidJjAldMpTR7XVxAN2gmynSX59JeNAebu9O93u7QkuDvXzZG9dW22bWURiHE1sU2zLqlnPoofV1VRFFxpWG2dtfm+L3lkNC9u2Zm0nVTmLVvn9r7v5d/uS/UkYt4b8tjibUiGVxazICMeSSkmtwBmlhYw="
		  }
	 * @return array(string => string)
	 */
	public function get3DSecureParams() {return $this->cfg('acs_params');}

	/**
	 * Адрес страницы авторизации эмитента банковской карты по технологии 3D-Secure.
	 * Поле присутствует, если для завершения транзакции с использованием банковской карты
	 * требуется авторизация по 3D-Secure.
	 * @return string
	 */
	public function get3DSecureUri() {return $this->cfg('acs_uri');}
	
	/**
	 * Номер счета плательщика.
	 * Присутствует при успешном переводе средств на счет другого пользователя Яндекс.Денег.
	 * @return string|null
	 */
	public function getCustomerAccountId() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_n_set($this->cfg('payer'));
		}
		return rm_n_get($this->{__METHOD__});
	}

	/**
	 * Рекомендуемое время спустя которое следует повторить запрос в миллисекундах.
	 * Поле присутствует при status=in_progress.
	 * @return int
	 */
	public function getDelayInMilliseconds() {return $this->cfg(self::$P__NEXT_RETRY);}

	/**
	 * «Сумма, полученная на счет получателем.
	 * Присутствует при успешном переводе средств на счет другого пользователя Яндекс.Денег.»
	 * @return Df_Core_Model_Money|null
	 */
	public function getPaymentAmount() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Core_Model_Money::i($this->cfg(self::$P__CREDIT_AMOUNT));
		}
		return $this->{__METHOD__};
	}

	/**
	 * Идентификатор платежа в платёжной системе.
	 * Присутствует только при успешном выполнении метода.
	 * @return string
	 */
	public function getPaymentExternalId() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->cfg('payment_id');
			df_result_string_not_empty($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return array(string => string)
	 */
	public function getReportAsArray() {
		if (!isset($this->{__METHOD__})) {
			/** @var array(string => string) $result */
			$result = array();
			$result['Успешна ли операция'] = rm_bts_r($this->isSuccessful());
			if (!$this->isSuccessful()) {
				$result['Диагностическое сообщение'] = $this->getErrorMessage();
			}
			if ($this->isSuccessful()) {
				if ($this->needRetry()) {
					$result['Требуется повтор'] = rm_bts_r(true);
					$result['Рекомендуемая пауза (мс)'] = $this->getDelayInMilliseconds();
				}
				if ($this->need3DSecure()) {
					$result['Требуется 3D-Secure'] = rm_bts_r(true);
					$result['Адрес 3D-Secure'] = $this->get3DSecureUri();
					$result['Параметры 3D-Secure'] =
						"\r\n" . rm_print_params($this->get3DSecureParams())
					;
				}
				$result = array_merge($result, array(
					'Идентификатор платежа в Яндекс.Деньгах' => $this->getPaymentExternalId()
					,'Полученная сумма' => $this->getPaymentAmount()->getAsString()
				));
				if ($this->getCustomerAccountId()) {
					$result['Номер счёта плательщика.'] = $this->getCustomerAccountId();
				}
				if (!is_null($this->getCustomerBalance())) {
					$result['Денег на счету покупателя'] = $this->getCustomerBalance()->getAsString();
				}
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return string
	 */
	public function getTransactionType() {
		return Mage_Sales_Model_Order_Payment_Transaction::TYPE_CAPTURE;
	}

	/**
	 * «Для завершения авторизации платежа с использованием банковской карты
	 * требуется дополнительная авторизация по технологии 3D-Secure.»
	 * @return bool
	 */
	public function need3DSecure() {return 'ext_auth_required' === $this->getStatusCode();}

	/**
	 * «Авторизация платежа не завершена.
	 * Приложению следует повторить запрос с теми же параметрами спустя некоторое время.»
	 * @return bool
	 */
	public function needRetry() {return 'in_progress' === $this->getStatusCode();}

	/**
	 * @override
	 * @return array(string => string)
	 */
	protected function getErrorMap() {
		if (!isset($this->{__METHOD__})) {$this->{__METHOD__} = array(
			'success' => 'Успешное выполнение.'
			,'refused' => 'Отказ в проведении платежа.'
			,'in_progress' =>
				'Авторизация платежа не завершена.'
				. '<br/>Приложению следует повторить запрос'
				. ' с теми же параметрами спустя некоторое время.'
			,'ext_auth_required' =>
				'Для завершения авторизации платежа с использованием банковской карты'
				. ' требуется дополнительная авторизация по технологии 3D-Secure.'
			,'contract_not_found' => 'Отсутствует выставленный контракт с заданным request_id.'
			,'not_enough_funds' =>
				'Недостаточно средств на счете плательщика.'
				. '<br/>Необходимо пополнить счет и провести новый платеж.'
			,'limit_exceeded' =>
				'Превышен один из лимитов на операции:'
				. '<br/>на сумму операции для выданного токена авторизации;'
				. '<br/>сумму операции за период времени для выданного токена авторизации;'
				. '<br/>ограничений Яндекс.Денег для различных видов операций.'
			,'money_source_not_available' =>
				'Запрошенный метод платежа (money_source) недоступен для данного платежа.'
			,'illegal_param_csc' => 'Отсутствует или указано недопустимое значение параметра csc.'
			,'payment_refused' => 'Магазин по какой-либо причине отказал в приеме платежа.'
			,'authorization_reject' =>
				'В авторизации платежа отказано. Возможные причины:'
				. '<br/>истек срок действия банковской карты;'
				. '<br/>банк-эмитент отклонил транзакцию по карте;'
				. '<br/>превышен лимит для этого пользователя;'
				. '<br/>транзакция с текущими параметрами запрещена для данного пользователя;'
				. '<br/>пользователь не принял Соглашение об использовании сервиса «Яндекс.Деньги».'
			,'account_blocked' => 'Счет пользователя заблокирован.'
			,'illegal_param_ext_auth_success_uri' =>
				'Отсутствует или указано недопустимое значение параметра ext_auth_success_uri.'
			,'illegal_param_ext_auth_fail_uri' =>
				'Отсутствует или указано недопустимое значение параметра ext_auth_fail_uri.'
		);}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return bool
	 */
	protected function isSuccessful() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				/**
				 * $this->need3DSecure() пока не учитываем, потому что приём банковских карт
				 * возможен только для зарегистрированных в Яндекс.Деньгах магазинах,
				 * а у меня такого магазина для тестирования нет.
				 */
				parent::isSuccessful() || $this->needRetry()
			;
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
			->_prop(self::$P__CREDIT_AMOUNT, self::V_FLOAT)
			->_prop(self::$P__NEXT_RETRY, self::V_NAT0)
		;
	}
	const _CLASS = __CLASS__;
	/** @var string */
	private static $P__CREDIT_AMOUNT = 'credit_amount';
	/** @var string */
	private static $P__NEXT_RETRY = 'next_retry';
}