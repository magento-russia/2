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
	 *
	 * @abstract
	 * @return string
	 */
	abstract protected function getRequestKeyOrderIncrementId();

	/**
	 * @override
	 * @return Df_Payment_Model_Action_Confirm
	 */
	public function process() {
		try {
			/**
			 * TODO Надо ли это здесь?
			 * Ведь запрос платёжной системы к магазину не относится к сессии покупателя.
			 * По-правильному здесь надо как-то загружать сессию покупателя.
			 */
			Df_Payment_Model_Redirector::s()->unsetRedirected();
			$this->getResponse()
				->setHeader(
					$name = Zend_Http_Client::CONTENT_TYPE
					,$value = $this->getContentType()
				)
			;
			$this->checkSignature();
			if ($this->needInvoice() && !$this->getOrder()->canInvoice()) {
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
					$invoice = $this->getOrder()->prepareInvoice();
					$invoice->register();
					if ($this->needCapture()) {
						$invoice->capture();
					}
					/** @var Mage_Core_Model_Resource_Transaction $transaction */
					$transaction = df_model(Df_Core_Const::CORE_RESOURCE_TRANSACTION_CLASS_MF);
					$transaction
						->addObject($invoice)
						->addObject($invoice->getOrder())
						->save()
					;
					$this->getOrder()
						->setState(
							Mage_Sales_Model_Order::STATE_PROCESSING
							,Mage_Sales_Model_Order::STATE_PROCESSING
							,rm_sprintf(
								$this->getMessage(self::CONFIG_KEY__MESSAGE__SUCCESS)
								,$invoice->getIncrementId()
							)
							,true
						)
						->save()
					;
					$this->getOrder()->sendNewOrderEmail();
				}
				$this->processResponseForSuccess();
			}
		}
		catch(Exception $e) {
			Mage::logException($e);
			$this->processException($e);
		}
		return $this;
	}

	/** @return Df_Payment_Model_Action_Confirm */
	protected function alternativeProcessWithoutInvoicing() {return $this;}

	/**
	 * @return Df_Payment_Model_Action_Confirm
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
				,$this->getServiceConfig()->getCurrencyCode()
				,$this->getRequestValuePaymentAmount()->getAsString()
				,$this->getServiceConfig()->getCurrencyCode()
			);
		}
		return $this;
	}

	/**
	 * @return Df_Payment_Model_Action_Confirm
	 * @throws Df_Core_Exception_Client
	 */
	protected function checkSignature() {
		if (!df_text()->areEqualCI(
			$this->getSignatureFromOwnCalculations()
			,$this->getRequestValueSignature()
		)) {
			df_error(strtr(
				$this->getMessage(self::CONFIG_KEY__MESSAGE__INVALID__SIGNATURE)
				,array(
					'{полученная подпись}' => $this->getRequestValueSignature()
					,'{ожидаемая подпись}' => $this->getSignatureFromOwnCalculations()
				)
			));
		}
		return $this;
	}

	/**
	 * @override
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

	/**
	 * @override
	 * @return Df_Sales_Model_Order
	 */
	protected function getOrder() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Sales_Model_Order::i();
			/** @var string|null $orderIncrementId */
			$orderIncrementId =
				rm_session_checkout()->getData(
					Df_Checkout_Const::SESSION_PARAM__LAST_REAL_ORDER_ID
				)
			;
			/**
			 * Вообще говоря, извлекать номер заказа из сессии — в корне ошибочно,
			 * потому что подтверждение платежа может прийти в совершенно другой сессии
			 * (например, в тестовом режиме Робокассы).
			 * Оставляем извлечение из сессии только ради обратной совместимости.
			 */
			if (!$orderIncrementId) {
				$orderIncrementId = $this->getRequestValueOrderIncrementId();
			}
			if (!$orderIncrementId) {
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
				$orderIncrementId =
					df_a($this->getRequest()->getParams(), $this->getRequestKeyOrderIncrementId())
				;
			}
			if (!$orderIncrementId) {
				df_error(
					"Некто пытается подтвердить оплату заказа, не указав номер заказа"
					."\nНазвание параметра, который должен содержать номер заказа: «%s»"
					."\nЗначения всех параметров:"
					."\n%s"
					,$this->getRequestKeyOrderIncrementId()
					,rm_print_params($this->getRequest()->getParams())
				);
			}
			$this->{__METHOD__}->loadByIncrementId($orderIncrementId);
			if (!$this->{__METHOD__}->getId()) {
				df_error(
					"Некто пытается подтвердить оплату отсутствующего в системе заказа «%s»."
					."\nВозможно, заказ был удалён администратором?"
					,$orderIncrementId
				);
			}
		}
		return $this->{__METHOD__};
	}

	/** @return Mage_Sales_Model_Order_Payment */
	protected function getOrderPayment() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->getOrder()->getPayment();
			df_assert($this->{__METHOD__} instanceof Mage_Sales_Model_Order_Payment);
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Core_Model_Money */
	protected function getPaymentAmountFromOrder() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				$this->getServiceConfig()->getOrderAmountInServiceCurrency(
					$this->getOrder()
				)
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param Exception $e
	 * @return string
	 */
	protected function getResponseTextForError(Exception $e) {return rm_ets($e);}

	/** @return string */
	protected function getResponseTextForSuccess() {return '';}

	/** @return Df_Payment_Model_Config_Area_Service */
	protected function getServiceConfig() {
		return $this->getPaymentMethod()->getRmConfig()->service();
	}

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
			$this->{__METHOD__} =
				Df_Core_Model_Money::i(rm_float($this->getRequestValuePaymentAmountAsString()))
			;
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
	protected function getResponsePassword() {
		/** @var string $result */
		$result = $this->getServiceConfig()->getResponsePassword();
		df_result_string($result);
		return $result;
	}

	/** @return bool */
	protected function needCheckPaymentAmount() {return $this->needInvoice();}

	/** @return bool */
	protected function needInvoice() {return true;}

	/**
	 * @param Exception $e
	 * @return Df_Payment_Model_Action_Confirm
	 */
	protected function logException(Exception $e) {
		$this->logExceptionStandard($e);
		$this->logExceptionToOrderHistory($e);
		return $this;
	}

	/**
	 * @param Exception $e
	 * @return Df_Payment_Model_Action_Confirm
	 */
	protected function logExceptionStandard(Exception $e) {
		$this->logFailureHighLevel(
			"При взаимодействии с платёжным шлюзом призошёл сбой.\n%s"
			."\nПараметры запроса:\n%s"
			,rm_ets($e)
			,rm_print_params($this->getRequest()->getParams())
		);
		// В низкоуровневый журнал исключительную ситуацию записываем
		// только если это сбой в программном коде.
		if (!($e instanceof Df_Payment_Exception_Client)) {
			$this->logFailureLowLevel($e);
		}
		return $this;
	}

	/**
	 * @param Exception $e
	 * @return Df_Payment_Model_Action_Confirm
	 */
	protected function logExceptionToOrderHistory(Exception $e) {
		if (
				isset($this->{__CLASS__ . '::getOrder'})
			&&
				rm_n_get($this->{__CLASS__ . '::getOrder'})
		) {
			$this->getOrder()->addStatusHistoryComment(df_no_escape(nl2br(rm_ets($e))));
			$this->getOrder()->setData(Df_Sales_Const::ORDER_PARAM__IS_CUSTOMER_NOTIFIED, false);
			$this->getOrder()->save();
		}
		return $this;
	}

	/**
	 * @param string|Exception $message
	 * @return Df_Payment_Model_Method_Base
	 */
	protected function logFailureHighLevel($message) {
		if (is_string($message)) {
			/**
			 * Обратите внимание,
			 * что функция @see func_get_args() не может быть параметром другой функции.
			 */
			$arguments = func_get_args();
			$message = rm_sprintf($arguments);
		}
		$this->getPaymentMethod()->logFailureHighLevel($message);
		return $this;
	}

	/**
	 * @param string|Exception $message
	 * @return Df_Payment_Model_Method_Base
	 */
	protected function logFailureLowLevel($message) {
		if (is_string($message)) {
			/**
			 * Обратите внимание,
			 * что функция @see func_get_args() не может быть параметром другой функции.
			 */
			$arguments = func_get_args();
			$message = rm_sprintf($arguments);
		}
		$this->getPaymentMethod()->logFailureLowLevel($message);
		return $this;
	}

	/** @return bool */
	protected function needCapture() {return true;}

	/**
	 * @param Exception $e
	 * @return Df_Payment_Model_Action_Confirm
	 */
	protected function processException(Exception $e) {
		$this->logException($e);
		$this->processResponseForError($e);
		return $this;
	}

	/**
	 * @return Df_Payment_Model_Action_Confirm
	 * @throws Mage_Core_Exception
	 */
	protected function processOrderCanNotInvoice() {
		/**
		 * Потомки могут перекрывать это поведение.
		 * Так делает Единая Касса.
		 */
		df_error('Платёжная система зачем-то повторно прислала оповещение об оплате.');
		return $this;
	}

	/**
	 * @param Exception $e
	 * @return Df_Payment_Model_Action_Confirm
	 */
	protected function processResponseForError(Exception $e) {
		$this->getResponse()->setBody($this->getResponseTextForError($e));
		return $this;
	}

	/** @return Df_Payment_Model_Action_Confirm */
	protected function processResponseForSuccess() {
		$this->getResponse()->setBody($this->getResponseTextForSuccess());
		return $this;
	}

	/** @return void */
	protected function redirectToCheckoutScreen() {
		$this->getResponse()->setRedirect(df_h()->payment()->url()->getCheckout());
	}

	/**
	 * @param Exception|Df_Payment_Exception_Client $e
	 * @return void
	 */
	protected function showExceptionOnCheckoutScreen(Exception $e) {
		/**
		 * Обратите внимание,
		 * что при возвращении на страницу Df_Checkout_Const::URL__CHECKOUT
		 * диагностическое сообщение надо добавлять в rm_session_core(),
		 * а не в rm_session_checkout(),
		 * потому что сообщения сессии checkout
		 * не отображаются в стандартной теме на странице checkout/onepage
		 */
		rm_session_core()->addError(nl2br(
				($e instanceof Df_Payment_Exception_Client)
			&&
				$e->needFraming()
			? strtr(
				$this->getPaymentMethod()->getRmConfig()->frontend()->getMessageFailure()
				,array('{сообщение от платёжного шлюза}' => rm_ets($e))
			)
			: rm_ets($e)
		));
	}

	/**
	 * @param string $message
	 * @return void
	 * @throws Df_Payment_Exception_Client
	 */
	protected function throwException($message) {throw new Df_Payment_Exception_Client($message);}

	const _CLASS = __CLASS__;
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