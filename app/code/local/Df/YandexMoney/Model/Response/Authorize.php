<?php
class Df_YandexMoney_Model_Response_Authorize extends Df_YandexMoney_Model_Response {
	/**
	 * «Адрес на который необходимо отправить пользователя для совершения необходимых действий
	 * в случае ошибки ext_action_required.»
	 * http://api.yandex.ru/money/doc/dg/reference/request-payment.xml
	 * @return string|null
	 */
	public function getActionUrl() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_n_set($this->cfg('ext_action_uri'));
		}
		return rm_n_get($this->{__METHOD__});
	}

	/**
	 * Текст описания платежа (контракт).
	 * Присутствует только при успешном выполнении платежа.
	 * Пример: «Оплата услуг ОАО Суперфон Поволжъе, номер +7-9xx-xxx-xx-xx, сумма 300.00 руб.».
	 * @return string
	 */
	public function getOperationDescription() {return $this->cfg('contract', '');}

	/**
	 * Идентификатор запроса платежа в платёжной системе.
	 * Присутствует только при успешном выполнении метода.
	 * @return string
	 */
	public function getOperationExternalId() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->cfg('request_id');
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
				$result = array_merge($result, array(
					'Доступные методы платежа' => df_csv_pretty($this->getMoneySources())
					,'Идентификатор платежа в платёжной системе' => $this->getOperationExternalId()
					,'Описание платежа' => $this->getOperationDescription()
				));
				if (!is_null($this->getCustomerBalance())) {
					$result['Денег на счету покупателя'] = $this->getCustomerBalance()->getAsString();
				}
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * «Доступные для приложения методы проведения платежа.
	 * Присутствует только при успешном выполнении метода.»
	 * Возможные методы проведения платежа:
			«wallet»:	Платеж со счета пользователя.
			«card»:		Платеж с привязанной к счету банковской карты.
	 * Если метод платежа доступен для данного магазина и разрешен пользователем,
	 * то в ответе будет присутствовать и название метода, и признак разрешения пользователем.
	 * Например:
	  		"card": { "allowed":"true" }
	 * Если метод доступен, но не разрешен пользователем,
	 * то в ответе будет присутствовать название метода и признак отсутствия разрешения пользователя.
	 * Например:
	 		"card": { "allowed":"false" }
	 * http://api.yandex.ru/money/doc/dg/reference/request-payment.xml#available-payment-methods
	 *
	 * Дополнение от 2014-08-09:
	 * Теперь вместо { "allowed":"true" } система возвращает { "allowed":true}.
	 * Может быть, это связано с обновлением интерпретатора PHP?
	 * http://magento-forum.ru/topic/4595/
	 *
	 * @return string[]
	 */
	public function getMoneySources() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = array();
			/** @var array(string => array(string => bool|string)) $sources */
			$sources = $this->cfg('money_source');
			foreach ($sources as $sourceName => $sourceProperties) {
				/** @var string $sourceName */
				/** @var array(string => bool|string) $sourceProperties */
				df_assert_string_not_empty($sourceName);
				df_assert_array($sourceProperties);
				if (rm_bool(dfa($sourceProperties, 'allowed'))) {
					$this->{__METHOD__}[]= $sourceName;
				}
			}
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return string
	 */
	public function getTransactionType() {return Mage_Sales_Model_Order_Payment_Transaction::TYPE_AUTH;}

	/**
	 * @override
	 * @return array(string => string)
	 */
	protected function getErrorMap() {
		if (!isset($this->{__METHOD__})) {$this->{__METHOD__} = array(
			'success' => 'Успешное выполнение.'
			,'refused' => 'Отказ в проведении платежа.'
			,'account_blocked' => 'Счет пользователя заблокирован.'
			,'authorization_reject' =>
				'В авторизации платежа отказано.'
				. '<br/>Возможные причины:'
				. '<br/>транзакция с текущими параметрами запрещена для данного пользователя;'
				. '<br/>пользователь не принял Соглашение об использовании сервиса «Яндекс.Деньги».'
			,'illegal_params' =>
				"Обязательные параметры платежа отсутствуют или имеют недопустимые значения."
				."\nТакой сбой возможен, в частности,"
				." когда кошелёк продавца совпадает с кошельком покупателя."
				."\nДля проверки работы модуля используйте разные кошельки для продавца и покупателя."
			,'illegal_param_label' => 'Параметр «label» имеет недопустимое значение.'
			,'illegal_param_to' => 'Параметр «to» имеет недопустимое значение.'
			,'illegal_param_amount' => 'Параметр «amount» имеет недопустимое значение.'
			,'illegal_param_amount_due' => 'Параметр «amount_due» имеет недопустимое значение.'
			,'illegal_param_comment' => 'Параметр «comment» имеет недопустимое значение.'
			,'illegal_param_message' => 'Параметр «message» имеет недопустимое значение.'
			,'illegal_param_expire_period' => 'Параметр «expire_period» имеет недопустимое значение.'
			,'limit_exceeded' =>
				'Превышен один из лимитов на операции:'
				. '<br/>на сумму операции для выданного токена авторизации;'
				. '<br/>сумму операции за период времени для выданного токена авторизации;'
				. '<br/>ограничений Яндекс.Денег для различных видов операций.'
			,'payee_not_found' =>
				'Получатель перевода не найден.'
				. ' Указанный счет не существует,'
				. ' или указан номер телефона или email,'
				. ' не связанный со счетом пользователя или получателя платежа.'
			,'payment_refused' =>
				'Магазин отказал в приеме платежа'
				.' (например пользователь попробовал заплатить за товар, которого нет в магазине).'
		);}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getErrorMessage() {
		/** @var string $result */
		if (!$this->isErrorCode_ActionRequired()) {
			$result = parent::getErrorMessage();
		}
		else {
			/** http://magento-forum.ru/topic/4612/ */
			df_assert_string_not_empty($this->getActionUrl());
			$result = strtr(
				"16 мая 2014 года вступил в силу новый российский закон:"
				." он вводит ограничения по электронным платежам для всех,"
				." кто не прошел идентификацию."
				."\nДля оплаты заказа посредством Яндекс.Денег"
				." Вам надо пройти идентификацию на сайте Яндекс.Денег по адресу:"
				."\n<a href='{адрес}'>{адрес}</a>"
				."\nПосле идентификации Вы сможете оплатить Ваш заказ Яндекс.Деньгами."
				."\nЕсли Вы не хотите проходить идентификацию,"
				." то Вы можете оплатить Ваш заказ другим способом."
				,array('{адрес}' => $this->getActionUrl())
			);
		}
		return $result;
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getExceptionClass() {
		return
			$this->isErrorCode_ActionRequired()
			? Df_YandexMoney_Exception_ActionRequired::_C
			: parent::getExceptionClass()
		;
	}

	/** @return bool */
	public function isErrorCode_ActionRequired() {return 'ext_action_required' === $this->getErrorCode();}

	/** @used-by Df_YandexMoney_Model_Request_Capture::_construct() */
	const _C = __CLASS__;
}