<?php
use Mage_Sales_Model_Order_Invoice as Invoice;
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
						$this->rAmount()->getAsString()
					!==
						$this->getPaymentAmountFromOrder()->getAsString()
				)
		) {
			$this->errorInvalidAmount();
		}
	}

	/**
	 * @return void
	 * @throws \Df\Core\Exception
	 */
	protected function checkSignature() {
		if (!df_t()->areEqualCI(
			$this->getSignatureFromOwnCalculations(), $this->getRequestValueSignature()
		)) {
			df_error($this->message('invalid/signature'), [
				'{полученная подпись}' => $this->getRequestValueSignature()
				,'{ожидаемая подпись}' => $this->getSignatureFromOwnCalculations()
			]);
		}
	}

	/**
	 * @override
	 * @see Df_Core_Model_Action::getContentType()
	 * @used-by Df_Core_Model_Action::getResponseLogFileExtension()
	 * @used-by Df_Core_Model_Action::processPrepare()
	 * @return string
	 */
	protected function getContentType() {return $this->const_('response/content-type');}

	/**           
	 * 2016-10-20            
	 * @used-by checkPaymentAmount()
	 * @used-by Df_EasyPay_Action_Confirm::checkPaymentAmount()  
	 * @used-by Df_WebPay_Action_Confirm::checkPaymentAmount() 
	 * @throws Df\Core\Exception
	 */
	protected function errorInvalidAmount() {
		df_error(
			$this->message('invalid/payment-amount')
			,$this->getPaymentAmountFromOrder()->getAsString()
			,$this->configS()->getCurrencyCode()
			,$this->rAmount()->getAsString()
			,$this->configS()->getCurrencyCode()
		);		
	}
	
	/**                          
	 * @used-by checkSignature() 
	 * @used-by errorInvalidAmount()
	 * @used-by messageSuccess()
	 * @param string $key
	 * @return string
	 */
	private function message($key) {return str_replace('\n', "<br/>", $this->const_('message/' . $key));}

	/**
	 * 2016-10-20
	 * @param Invoice $invoice
	 * @return string
	 */
	protected function messageSuccess(Invoice $invoice) {return
		df_sprintf($this->message('success'), $invoice->getIncrementId())
	;}	
	
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

	/**
	 * @used-by rAmountS()
	 * @used-by Df_PayOnline_Action_Confirm::getSignatureFromOwnCalculations()
	 * @return string
	 */
	protected function rkAmount() {return $this->const_('payment/amount');}

	/**
	 * @used-by Df_PayOnline_Action_Confirm::getSignatureFromOwnCalculations()
	 * @return string
	 */
	protected function rkCurrency() {return $this->const_('payment/currency-code');}

	/** @return string */
	protected function getRequestKeyPaymentTest() {return $this->const_('payment/test');}

	/** @return string */
	protected function getRequestKeyServicePaymentDate() {return
		$this->const_('payment_service/payment/date')
	;}

	/** @return string */
	protected function getRequestKeyServicePaymentId() {return
		$this->const_('payment_service/payment/id')
	;}

	/** @return string */
	protected function getRequestKeyServicePaymentState() {return
		$this->const_('payment_service/payment/state')
	;}

	/** @return string */
	protected function getRequestKeyShopId() {return $this->const_('payment_service/shop/id');}

	/** @return string */
	protected function getRequestKeySignature() {return $this->const_('request/signature');}

	/** @return string */
	protected function getRequestValueCustomerEmail() {return $this->paramC('customer/email');}

	/** @return string */
	protected function getRequestValueCustomerName() {return $this->paramC('customer/name');}

	/** @return string */
	protected function getRequestValueOrderCustomerPhone() {return $this->paramC('customer/phone');}

	/** @return string */
	protected function getRequestValueOrderIncrementId() {
		/** @var string $result */
		$result = $this->getRequest()->getParam($this->getRequestKeyOrderIncrementId());
		df_result_string($result);
		return $result;
	}

	/** @return Df_Core_Model_Money */
	protected function rAmount() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_money(df_float($this->rAmountS()));
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	protected function rAmountS() {return $this->param($this->rkAmount());}

	/** @return string */
	protected function rCurrencyC() {return $this->param($this->rkCurrency());}

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
		$this->method()->logFailureHighLevel($message);
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
		$this->method()->logFailureLowLevel($message);
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
		if (df_my()) {
			/** @var string $module */
			$module = df_module_name();
			df_report("{$module}-{date}-{time}.log", df_json_encode_pretty(df_request()));
		}
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
					,$this->messageSuccess($invoice)
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
	 * @return void
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
	 * @used-by Df_Alfabank_Action_CustomerReturn::processException()
	 * @used-by Df_Avangard_Action_CustomerReturn::processException()
	 * @used-by Df_Psbank_Action_CustomerReturn::processException()
	 * @used-by Df_YandexMoney_Action_CustomerReturn::processException()
	 * @return void
	 */
	protected function redirectToCheckout() {$this->redirect(RM_URL_CHECKOUT);}

	/**
	 * @used-by Df_Alfabank_Action_CustomerReturn::processResponseForError()
	 * @used-by Df_Avangard_Action_CustomerReturn::processResponseForError()
	 * @used-by Df_Psbank_Action_CustomerReturn::_process()
	 * @return void
	 */
	protected function redirectToFail() {$this->redirectRaw(df_url_checkout_fail());}

	/**
	 * @used-by Df_Alfabank_Action_CustomerReturn::_process()
	 * @return void
	 */
	protected function redirectToSuccess() {$this->redirectRaw(df_url_checkout_success());}

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
			? strtr($this->method()->configF()->getMessageFailure(), array(
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
	 * 2016-10-20
	 * @param string $key
	 * @param string|null $d [optional]
	 * @return string
	 */
	private function param($key, $d = null) {return $this->getRequest()->getParam($key, $d);}

	/**
	 * 2016-10-20
	 * @param string $key
	 * @param string|null $d [optional]
	 * @return string
	 */
	private function paramC($key, $d = null) {return $this->param($this->const_($key), $d);}

	/**
	 * @used-by logExceptionToOrderHistory()
	 * @used-by order()
	 * @var Df_Sales_Model_Order
	 */
	private $_order = null;
}