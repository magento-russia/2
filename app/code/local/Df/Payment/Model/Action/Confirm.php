<?php
abstract class Df_Payment_Model_Action_Confirm extends Df_Payment_Model_Action_Abstract {
	/**
	 * @abstract
	 * @return string
	 */
	abstract protected function getSignatureFromOwnCalculations();

	/**
	 * Вынуждены делать метод абстрактным.
	 * Использовать getConst нельзя из-за рекурсии.
	 * @abstract
	 * @return string
	 */
	abstract protected function getRequestKeyOrderIncrementId();

	/** @return void */
	protected function alternativeProcessWithoutInvoicing() {}

	/**
	 * @return void
	 * @throws Exception
	 */
	protected function checkPaymentAmount() {
		/**
		 * Проверяем размер оплаты только в случае создание объекта-счёта.
		 * Если счёт уже был создан ранее, то $this->getPaymentAmountFromOrder() может вернуть 0,
		 * и проверка в том виде, как она есть сейчас, всё равно не сработает.
		 */
		if (
				$this->needCheckPaymentAmount()
			&&
				(
						$this->getRequestValuePaymentAmount()->getAsString()
					!==
						$this->getPaymentAmountFromOrder()->getAsString()
				)
		) {
			df_error(
				$this->getMessage(self::CONFIG_KEY__MESSAGE__INVALID__PAYMENT_AMOUNT)
				,$this->getPaymentAmountFromOrder()->getAsString()
				,$this->configS()->getCurrencyCode()
				,$this->getRequestValuePaymentAmount()->getAsString()
				,$this->configS()->getCurrencyCode()
			);
		}
	}

	/**
	 * @return void
	 * @throws Df_Core_Exception
	 */
	protected function checkSignature() {
		if (!df_t()->areEqualCI(
			$this->getSignatureFromOwnCalculations(), $this->getRequestValueSignature()
		)) {
			df_error($this->getMessage(self::CONFIG_KEY__MESSAGE__INVALID__SIGNATURE), array(
				'{полученная подпись}' => $this->getRequestValueSignature()
				,'{ожидаемая подпись}' => $this->getSignatureFromOwnCalculations()
			));
		}
	}

	/**
	 * @override
	 * @see Df_Core_Model_Action::getContentType()
	 * @used-by Df_Core_Model_Action::getResponseLogFileExtension()
	 * @used-by Df_Core_Model_Action::processPrepare()
	 * @return string
	 */
	protected function getContentType() {
		return $this->getConst(self::CONFIG_KEY__RESPONSE__CONTENT_TYPE);
	}

	/**
	 * @param string $configKey
	 * @return string
	 */
	protected function getMessage($configKey) {
		df_param_string($configKey, 0);
		return str_replace('\n', "<br/>", $this->getConst($configKey));
	}

	/** @return Df_Core_Model_Money */
	protected function getPaymentAmountFromOrder() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->configS()->getOrderAmountInServiceCurrency($this->order());
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param Exception $e
	 * @return string
	 */
	protected function getResponseTextForError(Exception $e) {return df_ets($e);}

	/** @return string */
	protected function getResponseTextForSuccess() {return '';}

	/** @return Df_Payment_Config_Area_Service */
	protected function configS() {return $this->method()->configS();}

	/** @return string */
	protected function getRequestKeyCustomerEmail() {
		return $this->getConst(self::CONFIG_KEY__CUSTOMER__EMAIL);
	}

	/** @return string */
	protected function getRequestKeyCustomerName() {
		return $this->getConst(self::CONFIG_KEY__CUSTOMER__NAME);
	}

	/** @return string */
	protected function getRequestKeyCustomerPhone() {
		return $this->getConst(self::CONFIG_KEY__CUSTOMER__PHONE);
	}

	/** @return string */
	protected function getRequestKeyPaymentAmount() {
		return $this->getConst(self::CONFIG_KEY__PAYMENT__AMOUNT);
	}

	/** @return string */
	protected function getRequestKeyPaymentCurrencyCode() {
		return $this->getConst(self::CONFIG_KEY__PAYMENT__CURRENCY_CODE);
	}

	/** @return string */
	protected function getRequestKeyPaymentTest() {
		return $this->getConst(self::CONFIG_KEY__PAYMENT__TEST);
	}

	/** @return string */
	protected function getRequestKeyServicePaymentDate() {
		return $this->getConst(self::CONFIG_KEY__PAYMENT_SERVICE__PAYMENT__DATE);
	}

	/** @return string */
	protected function getRequestKeyServicePaymentId() {
		return $this->getConst(self::CONFIG_KEY__PAYMENT_SERVICE__PAYMENT__ID);
	}

	/** @return string */
	protected function getRequestKeyServicePaymentState() {
		return $this->getConst(self::CONFIG_KEY__PAYMENT_SERVICE__PAYMENT__STATE);
	}

	/** @return string */
	protected function getRequestKeyShopId() {
		return $this->getConst(self::CONFIG_KEY__PAYMENT_SERVICE__SHOP__ID);
	}

	/** @return string */
	protected function getRequestKeySignature() {
		return $this->getConst(self::CONFIG_KEY__REQUEST__SIGNATURE);
	}

	/** @return string */
	protected function getRequestValueCustomerEmail() {
		/** @var string $result */
		$result = $this->getRequest()->getParam($this->getRequestKeyCustomerEmail());
		df_result_string($result);
		return $result;
	}

	/** @return string */
	protected function getRequestValueCustomerName() {
		/** @var string $result */
		$result = $this->getRequest()->getParam($this->getRequestKeyCustomerName());
		df_result_string($result);
		return $result;
	}

	/** @return string */
	protected function getRequestValueOrderCustomerPhone() {
		/** @var string $result */
		$result = $this->getRequest()->getParam($this->getRequestKeyCustomerPhone());
		df_result_string($result);
		return $result;
	}

	/** @return string */
	protected function getRequestValueOrderIncrementId() {
		/** @var string $result */
		$result = $this->getRequest()->getParam($this->getRequestKeyOrderIncrementId());
		df_result_string($result);
		return $result;
	}

	/** @return Df_Core_Model_Money */
	protected function getRequestValuePaymentAmount() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_money(df_float($this->getRequestValuePaymentAmountAsString()));
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	protected function getRequestValuePaymentAmountAsString() {
		return $this->getRequest()->getParam($this->getRequestKeyPaymentAmount());
	}

	/** @return string */
	protected function getRequestValuePaymentCurrencyCode() {
		return $this->getRequest()->getParam($this->getRequestKeyPaymentCurrencyCode());
	}

	/** @return string */
	protected function getRequestValuePaymentTest() {
		/** @var string $result */
		$result = $this->getRequest()->getParam($this->getRequestKeyPaymentTest());
		df_result_string($result);
		return $result;
	}

	/** @return string */
	protected function getRequestValueServicePaymentDate() {
		/** @var string $result */
		$result = $this->getRequest()->getParam($this->getRequestKeyServicePaymentDate());
		df_result_string($result);
		return $result;
	}

	/** @return string */
	protected function getRequestValueServicePaymentId() {
		/** @var string $result */
		$result = $this->getRequest()->getParam($this->getRequestKeyServicePaymentId());
		df_result_string($result);
		return $result;
	}

	/** @return string */
	protected function getRequestValueServicePaymentState() {
		/** @var string $result */
		$result = $this->getRequest()->getParam($this->getRequestKeyServicePaymentState());
		df_result_string($result);
		return $result;
	}

	/** @return string */
	protected function getRequestValueShopId() {
		/** @var string $result */
		$result = $this->getRequest()->getParam($this->getRequestKeyShopId());
		df_result_string($result);
		return $result;
	}

	/** @return string */
	protected function getRequestValueSignature() {
		/** @var string $result */
		$result = $this->getRequest()->getParam($this->getRequestKeySignature());
		df_result_string($result);
		return $result;
	}

	/** @return string */
	protected function getResponsePassword() {return $this->configS()->getResponsePassword();}

	/** @return bool */
	protected function needCheckPaymentAmount() {return $this->needInvoice();}

	/** @return bool */
	protected function needInvoice() {return true;}

	/**
	 * @param Exception $e
	 * @return void
	 */
	protected function logException(Exception $e) {
		$this->logExceptionStandard($e);
		$this->logExceptionToOrderHistory($e);
	}

	/**
	 * @param Exception $e
	 * @return void
	 */
	protected function logExceptionStandard(Exception $e) {
		$this->logFailureHighLevel(
			"При взаимодействии с платёжным шлюзом призошёл сбой.\n%s"
			."\nПараметры запроса:\n%s"
			,df_ets($e)
			,df_print_params($this->getRequest()->getParams())
		);
		// В низкоуровневый журнал исключительную ситуацию записываем
		// только если это сбой в программном коде.
		if (!($e instanceof Df_Payment_Exception)) {
			$this->logFailureLowLevel($e);
		}
	}

	/**
	 * @param Exception $e
	 * @return void
	 */
	protected function logExceptionToOrderHistory(Exception $e) {
		if ($this->_order) {
			$this->comment(df_no_escape(df_t()->nl2br(df_ets($e))));
		}
	}

	/**
	 * @param string|Exception $message
	 * @return void
	 */
	protected function logFailureHighLevel($message) {
		if (is_string($message)) {
			/** @var mixed[] $arguments */
			$arguments = func_get_args();
			$message = df_format($arguments);
		}
		$this->getMethod()->logFailureHighLevel($message);
	}

	/**
	 * @param string|Exception $message
	 * @return void
	 */
	protected function logFailureLowLevel($message) {
		if (is_string($message)) {
			/** @var mixed[] $arguments */
			$arguments = func_get_args();
			$message = df_format($arguments);
		}
		$this->getMethod()->logFailureLowLevel($message);
	}

	/** @return bool */
	protected function needAddExceptionToSession() {return false;}

	/** @return bool */
	protected function needCapture() {return true;}

	/** @return bool */
	protected function needRethrowException() {return false;}

	/**
	 * @override
	 * @see Df_Payment_Model_Action_Abstract::order()
	 * @used-by Df_Payment_Model_Action_Abstract::addAndSaveStatusHistoryComment()
	 * @used-by Df_Payment_Model_Action_Abstract::getMethod()
	 * @used-by Df_Payment_Model_Action_Abstract::getPayment()
	 * @used-by getPaymentAmountFromOrder()
	 * @used-by _process()
	 * @return Df_Sales_Model_Order
	 */
	protected function order() {
		if (!$this->_order) {
			$this->_order = Df_Sales_Model_Order::ldi($this->orderIId(), false);
			if (!$this->_order) {
				df_error(
					"Некто пытается подтвердить оплату отсутствующего в системе заказа «%s»."
					."\nВозможно, заказ был удалён администратором?"
					,$this->orderIId()
				);
			}
		}
		return $this->_order;
	}

	/**
	 * @override
	 * @param Exception $e
	 * @return void
	 */
	protected function processException(Exception $e) {
		$this->logException($e);
		$this->processResponseForError($e);
		parent::processException($e);
	}

	/**
	 * @override
	 * @see Df_Core_Model_Action::_process()
	 * @used-by Df_Core_Model_Action::process()
	 * @return void
	 */
	protected function _process() {
		/**
		 * TODO Надо ли это здесь?
		 * Ведь запрос платёжной системы к магазину не относится к сессии покупателя.
		 * По-правильному здесь надо как-то загружать сессию покупателя.
		 */
		Df_Payment_Redirected::off();
		$this->checkSignature();
		if ($this->needInvoice() && !$this->order()->canInvoice()) {
			/**
			 * Бывают платёжные системы (например, «Единая касса»),
			 * которые, согласно их документации,
			 * могут несколько раз присылать подтверждение оплаты покупателем
			 * одного и того же заказа.
			 *
			 * Так вот, данная проверка гарантирует, что платёжный модуль не будет пытаться
			 * принять повторно оплату за уже оплаченный заказ.
			 *
			 * Обратите внимание, что проверку заказа на оплаченность
			 * надо сделать до вызова метода checkPaymentAmount,
			 * потому что иначе требуемая к оплате сумма будет равна нулю,
			 * и checkPaymentAmount будет сравнивать сумму от платёжной системы с нулём.
			 */
			$this->processOrderCanNotInvoice();
		}
		else {
			$this->checkPaymentAmount();
			if (!$this->needInvoice()) {
				$this->alternativeProcessWithoutInvoicing();
			}
			else {
				/** @var Mage_Sales_Model_Order_Invoice $invoice */
				$invoice = $this->order()->prepareInvoice();
				$invoice->register();
				if ($this->needCapture()) {
					$invoice->capture();
				}
				$this->saveInvoice($invoice);
				$this->order()->setState(
					Mage_Sales_Model_Order::STATE_PROCESSING
					,Mage_Sales_Model_Order::STATE_PROCESSING
					,df_sprintf(
						$this->getMessage(self::CONFIG_KEY__MESSAGE__SUCCESS), $invoice->getIncrementId()
					)
					,true
				);
				$this->order()->save();
				$this->order()->sendNewOrderEmail();
			}
			$this->processResponseForSuccess();
		}
	}

	/**
	 * Потомки могут перекрывать это поведение.
	 * Так делает Единая Касса.
	 * @return Df_Payment_Model_Action_Confirm
	 * @throws Mage_Core_Exception
	 */
	protected function processOrderCanNotInvoice() {
		df_error('Платёжная система зачем-то повторно прислала оповещение об оплате.');
	}

	/**
	 * @param Exception $e
	 * @return void
	 */
	protected function processResponseForError(Exception $e) {
		$this->getResponse()->setBody($this->getResponseTextForError($e));
	}

	/** @return Df_Payment_Model_Action_Confirm */
	protected function processResponseForSuccess() {
		$this->getResponse()->setBody($this->getResponseTextForSuccess());
		return $this;
	}

	/**
	 * @used-by Df_Alfabank_Model_Action_CustomerReturn::processException()
	 * @used-by Df_Avangard_Model_Action_CustomerReturn::processException()
	 * @used-by Df_Psbank_Model_Action_CustomerReturn::processException()
	 * @used-by Df_YandexMoney_Model_Action_CustomerReturn::processException()
	 * @return void
	 */
	protected function redirectToCheckout() {$this->redirect(RM_URL_CHECKOUT);}

	/**
	 * @used-by Df_Alfabank_Model_Action_CustomerReturn::processResponseForError()
	 * @used-by Df_Avangard_Model_Action_CustomerReturn::processResponseForError()
	 * @used-by Df_Psbank_Model_Action_CustomerReturn::_process()
	 * @return void
	 */
	protected function redirectToFail() {$this->redirectRaw(rm_url_checkout_fail());}

	/**
	 * @used-by Df_Alfabank_Model_Action_CustomerReturn::_process()
	 * @return void
	 */
	protected function redirectToSuccess() {$this->redirectRaw(rm_url_checkout_success());}

	/**
	 * @param Exception|Df_Payment_Exception $e
	 * @return void
	 */
	protected function showExceptionOnCheckoutScreen(Exception $e) {
		/**
		 * Обратите внимание,
		 * что при возвращении на страницу RM_URL_CHECKOUT
		 * диагностическое сообщение надо добавлять в df_session_core(),
		 * а не в df_session_checkout(),
		 * потому что сообщения сессии checkout
		 * не отображаются в стандартной теме на странице checkout/onepage
		 */
		df_session_core()->addError(df_t()->nl2br(
			$e instanceof Df_Payment_Exception && $e->needFraming()
			? strtr($this->getMethod()->configF()->getMessageFailure(), array(
				'{сообщение от платёжного шлюза}' => df_ets($e))
			)
			: df_ets($e)
		));
	}

	/**
	 * @param string $message
	 * @return void
	 * @throws Df_Payment_Exception
	 */
	protected function throwException($message) {df_error(new Df_Payment_Exception($message));}

	/**
	 * @used-by order()
	 * @return string
	 */
	private function orderIId() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $result */
			$result = df_last_order_iid();
			/**
			 * Вообще говоря, извлекать номер заказа из сессии — в корне ошибочно,
			 * потому что подтверждение платежа может прийти в совершенно другой сессии
			 * (например, в тестовом режиме Робокассы).
			 * Оставляем извлечение из сессии только ради обратной совместимости.
			 */
			if (!$result) {
				$result = $this->getRequestValueOrderIncrementId();
			}
			if (!$result) {
				/**
				 * Мистика.
				 * Почему то при включенной компиляции
				 * вызов $this->getRequestValueOrderIncrementId() возвращает пустое значение,
				 * и в то же время $this->getRequest()->getParams() содержит требуемое значение.
				 * Сидел, думал — не смог объяснить,
				 * поэтому добавил возможность получения $orderIncrementId через df_a.
				 * Такой эффект заметил только в версии 2.20.0 и только при включенной компиляции
				 * в двух магазинах: antonshop.com и mamamallm.ru
				 */
				$result = dfa($this->getRequest()->getParams(), $this->getRequestKeyOrderIncrementId());
			}
			if (!$result) {
				df_error(
					"Некто пытается подтвердить оплату заказа, не указав номер заказа"
					."\nНазвание параметра, который должен содержать номер заказа: «%s»"
					."\nЗначения всех параметров:"
					."\n%s"
					,$this->getRequestKeyOrderIncrementId()
					,df_print_params($this->getRequest()->getParams())
				);
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @used-by logExceptionToOrderHistory()
	 * @used-by order()
	 * @var Df_Sales_Model_Order
	 */
	private $_order = null;

	const CONFIG_KEY__ADMIN__ORDER__INCREMENT_ID = 'admin/order/increment-id';
	const CONFIG_KEY__CUSTOMER__EMAIL = 'customer/email';
	const CONFIG_KEY__CUSTOMER__NAME = 'customer/name';
	const CONFIG_KEY__CUSTOMER__PHONE = 'customer/phone';
	const CONFIG_KEY__MESSAGE__INVALID__ORDER = 'message/invalid/order';
	const CONFIG_KEY__MESSAGE__INVALID__PAYMENT_AMOUNT = 'message/invalid/payment-amount';
	const CONFIG_KEY__MESSAGE__INVALID__SIGNATURE = 'message/invalid/signature';
	const CONFIG_KEY__MESSAGE__SUCCESS = 'message/success';
	const CONFIG_KEY__PAYMENT__AMOUNT = 'payment/amount';
	const CONFIG_KEY__PAYMENT__CURRENCY_CODE = 'payment/currency-code';
	const CONFIG_KEY__PAYMENT__TEST = 'payment/test';
	const CONFIG_KEY__PAYMENT_SERVICE__PAYMENT__DATE = 'payment_service/payment/date';
	const CONFIG_KEY__PAYMENT_SERVICE__PAYMENT__ID = 'payment_service/payment/id';
	const CONFIG_KEY__PAYMENT_SERVICE__PAYMENT__STATE = 'payment_service/payment/state';
	const CONFIG_KEY__PAYMENT_SERVICE__SHOP__ID = 'payment_service/shop/id';
	const CONFIG_KEY__REQUEST__SIGNATURE = 'request/signature';
	const CONFIG_KEY__RESPONSE__CONTENT_TYPE = 'response/content-type';
}