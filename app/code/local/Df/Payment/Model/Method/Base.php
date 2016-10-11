<?php
abstract class Df_Payment_Model_Method_Base
	extends Mage_Payment_Model_Method_Abstract
	implements Df_Core_Model_PaymentShipping_Method {
	/**
	 * @override
	 * @param array|Varien_Object $data
	 * @return Df_Payment_Model_Method_Base
	 */
	public function assignData($data) {
		parent::assignData($data);
		if (!is_array($data)) {
			df_assert($data instanceof Varien_Object);
			$data = $data->getData();
		}
		foreach ($this->getCustomInformationKeys() as $customInformationKey) {
			/** @var string $customInformationKey */
			df_assert_string($customInformationKey);
			/** @var string|null $value */
			$value = df_a($data, $customInformationKey);
			if (!is_null($value)) {
				$this->getInfoInstance()->setAdditionalInformation($customInformationKey, $value);
			}
		}
		return $this;
	}

	/**
	 * Я так понимаю, возвращение этим методом значения true означает,
	 * что платёжный шлюз поддерживает возможность предварительной блокировки средств покупателя
	 * (с возможностью их последующего списания со счёта покупателя магазином).
	 *
	 * Причём роль этого метода — очень маленькая,
	 * он вызывается только методом @see Mage_Payment_Model_Method_Abstract::authorize(),
	 * который возбуждает исключительную ситуацию, если canAuthorize вернёт false.
	 *
	 * Обратите внимание, что многие стандартные модули оплаты Magento CE
	 * переопределяют метод @see Mage_Payment_Model_Method_Abstract::authorize(),
	 * не вызывая родительский, поэтому у них флага canAuthorize не играет никакой роли вообще.
	 *
	 * Метод @see Mage_Sales_Model_Order_Payment::place()
	 * вызывает один из методов
	 * @see Mage_Payment_Model_Method_Abstract::authorize()
	 * @see Mage_Payment_Model_Method_Abstract::capture()
	 * @see Mage_Payment_Model_Method_Abstract::order()
	 * в зависимости от указанного в настройках платёжного модуля значения параметра payment_action
	 * Для получения значения этой настройки вызывается метод
	 * @see Mage_Payment_Model_Method_Abstract::getConfigPaymentAction().
	 *
	 * Обратите внимание, что Df_Payment_Model_Method_Base::getConfigPaymentAction()
	 * всегда возвращает true, тем самым метод @see Mage_Sales_Model_Order_Payment::place()
	 * не вызывает authorize/capture/order.
	 *
	 * Обратите внимание также, что кроме @see Mage_Sales_Model_Order_Payment::place()
	 * метод authorize никто не вызывает,
	 * поэтому для модулей Российской сборки Magento данный метод вообще не будет использоваться.
	 * @override
	 * @return bool
	 */
	public function canAuthorize() {return false;}

	/**
	 * Важно вернуть true, чтобы
	 * @see Df_Payment_Model_Action_Confirm::process() и другие аналогичные методы
	 * (например, @see Df_Alfabank_Model_Action_CustomerReturn::process())
	 * могли вызвать @see Mage_Sales_Model_Order_Invoice::capture(),
	 * а также чтобы можно было проводить двуступенчатую оплату:
	 * резервирование средств непосредственно в процессе оформления заказа
	 * и снятие средств посредством нажатия кнопки «Принять оплату» («Capture»)
	 * на административной странице счёта.
	 *
	 * Обратите внимание, что двуступенчатая оплата
	 * имеет смысл не только для дочернего данному класса @see Df_Payment_Model_Method_WithRedirect,
	 * но и для других прямых детей класса @see Df_Payment_Model_Method_Base.
	 * @todo Например, правильным будет сделать оплату двуступенчатой для модуля «Квитанция Сбербанка»,
	 * потому что непосредственно по завершению заказа
	 * неправильно переводить счёт в состояние «Оплачен»
	 * (ведь он не оплачен! покупатель получил просто ссылку на квитанцию и далеко неочевидно,
	 * что он оплатит эту квитанцию).
	 * Вместо этого правильно будет оставлять счёт в открытом состоянии
	 * и переводить его в оплаченное состояние только после оплаты.
	 * @override
	 * @return bool
	 */
	public function canCapture() {return true;}

	/**
	 * @override
	 * @return bool
	 */
	public function canOrder() {return false;}

	/**
	 * Результат метода говорит системе о том, поддерживает ли способ оплаты
	 * автоматизированный возврат оплаты покупателю
	 * @override
	 * @return bool
	 */
	public function canRefund() {return false;}

	/**
	 * Результат метода говорит системе о том, поддерживает ли способ оплаты
	 * автоматизированный возврат части оплаты покупателю.
	 * Если способ оплаты частичный возврат допускает или же вообще возврата не допускает,
	 * то на странице документа-возврата появляется возможность редактирования
	 * количества возвращаемого товара.
	 * @see Mage_Adminhtml_Block_Sales_Order_Creditmemo_Create_Items::canEditQty():
		public function canEditQty() {
		 if ($this->getCreditmemo()->getOrder()->getPayment()->canRefund()) {
			 return $this->getCreditmemo()->getOrder()->getPayment()->canRefundPartialPerInvoice();
		 }
		 return true;
	 }
	 * @override
	 * @return bool
	 */
	public function canRefundPartialPerInvoice() {return false;}

	/**
	 * Результат метода говорит системе о том, поддерживает ли способ оплаты
	 * функциональность одобрения / отклонения платежей
	 * (в частности, такая функция есть в PayPal).
	 * @override
	 * @param Mage_Payment_Model_Info $payment
	 * @return bool
	 */
	public function canReviewPayment(Mage_Payment_Model_Info $payment) {return false;}

	/**
	 * Результат метода говорит системе о том, поддерживает ли способ оплаты
	 * автоматизированное разблокирование (возврат покупателю)
	 * ранее зарезервированных (но не снятых со счёта покупателя) средств
	 * @override
	 * @param Varien_Object $payment
	 * @return bool
	 */
	public function canVoid(Varien_Object $payment) {
		return false;
	}

	/**
	 * Этот метод вызывается только при двуступенчатой оплате,
	 * когда непосредственно в процессе оформления заказа
	 * средства с карты покупателя не были списаны, а были лишь зарезервированы,
	 * и когда затем администратор на административной странице счёта
	 * нажимает кнопку «Принять оплату» («Capture»).
	 * @see Mage_Adminhtml_Block_Sales_Order_Invoice_View::__construct():
		if ($this->_isAllowedAction('capture') && $this->getInvoice()->canCapture()) {
			 $this->_addButton('capture', array(
				 'label'     => Mage::helper('sales')->__('Capture'),
				 'class'     => 'save',
				 'onclick'   => 'setLocation(\''.$this->getCaptureUrl().'\')'
				 )
			 );
		 }
	 * @see Mage_Adminhtml_Sales_Order_InvoiceController::captureAction()
		$invoice->capture();
	 * @see Mage_Sales_Model_Order_Payment::capture()
		if (!$invoice->getIsPaid() && !$this->getIsTransactionPending()) {
			$this->getMethodInstance()->setStore($order->getStoreId())->capture($this, $amountToCapture);
		}
	 *
	 * Обратите внимание, на реальные типы аргументов:
	 * аргумент $payment — это всегда объект класса Mage_Sales_Model_Order_Payment.
	 * аргумент $amount — это вовсе не с float, как описано в базовом классе, а строка:
	 * @see Mage_Sales_Model_Order_Payment::capture():
			$amountToCapture = $this->_formatAmount($invoice->getBaseGrandTotal());
	 * @see Mage_Sales_Model_Order_Payment::_formatAmount():
		protected function _formatAmount($amount, $asFloat = false) {
		  $amount = Mage::app()->getStore()->roundPrice($amount);
		  return !$asFloat ? (string)$amount : $amount;
		}
	 * Т.к. метод @see Mage_Sales_Model_Order_Payment::refund() вызывает метод
	 * @see Mage_Sales_Model_Order_Payment::_formatAmount() без второго аргумента,
	 * то результатом вызова _formatAmount() будет именно строка.
	 *
	 * Обратите внимание, что размерностью $amount является не валюта заказа,
	 * а учётная валюта магазина:
	 * @see Mage_Sales_Model_Order_Payment::capture():
			$amountToCapture = $this->_formatAmount($invoice->getBaseGrandTotal());
	 *
	 * @override
	 * @param Varien_Object $payment
	 * @param string $amount
	 * @return Df_Payment_Model_Method_Base
	 */
	public function capture(Varien_Object $payment, $amount) {
		/** @var Mage_Sales_Model_Order_Payment $payment */
		/** @var string $amount */
		df_assert($payment instanceof Mage_Sales_Model_Order_Payment);
		/**
		 * @see Mage_Payment_Model_Method_Abstract::capture()
		 * контролирует допустимость вызова метода capture():
		 * если способ оплаты не поддерживает возврат средств покупателю
		 * (@see Df_Payment_Model_Method_Base::canCapture()),
		 * то Mage_Payment_Model_Method_Abstract::capture() возбудит исключительную ситуацию.
		 */
		parent::capture($payment, $amount);
		// Важно!
		if (df_is_admin()) {
			$this->doTransaction($payment, $amount, $handlerSuffix = 'Model_Capturer');
		}
		return $this;
	}

	/**
	 * Возвращает глобальный идентификатор способа оплаты.
	 * Этот идентификатор используется системой,
	 * например, для обозначения способа оплаты в объекте-заказе.
	 * В данном методе глобальный идентификатор способа оплаты формируется
	 * добавлением приставки «df-» к внутреннему идентификатору способа оплаты
	 * внутри Российской сборки.
	 * @override
	 * @return string
	 */
	public function getCode() {
		/**
		 * Переменна _code объявлена в родительском классе
		 * @see Mage_Payment_Model_Method_Abstract::$_code
		 */
		if (!isset($this->_code)) {
			$this->_code = self::getCodeByRmId($this->getRmId());
		}
		return $this->_code;
	}

	/**
	 * Получаем заданное ранее администратором
	 * значение конкретной настройки платёжного способа
	 * @override
	 * @param string $field
	 * @param int|string|null|Mage_Core_Model_Store $storeId[optional]
	 * @return mixed
	 */
	public function getConfigData($field, $storeId = null) {
		df_param_string($field, 0);
		df()->assert()->storeAsParameterForGettingConfigValue($storeId);
		/** @var mixed $result */
		$result = $this->getRmConfig($storeId)->getVar($field);
		return $result;
	}

	/**
	 * @see Mage_Sales_Model_Order_Payment::place()
	 * @override
	 * @return string|bool
	 */
	public function getConfigPaymentAction() {
		/**
		 * Возвращением true мы обходим стандартную обработку authorize/capture/order
		 * метода @see Mage_Sales_Model_Order_Payment::place()
		 */
		return true;
	}
	
	/**
	 * @see Df_Payment_Model_ConfigManager::getPostProcessTemplates()
	 * @return array(string => string)
	 */
	public function getConfigTemplates() {
		if (!isset($this->{__METHOD__})) {$this->{__METHOD__} = array(
			'{название платёжного шлюза в дательном падеже}' => $this->getNameInCaseDative()
			,'{название платёжного шлюза в именительном падеже}' => $this->getNameInNominativeCase()
			,'{название платёжного шлюза в родительном падеже}' => $this->getNameInCaseGenitive()
			,'{название платёжного шлюза в творительном падеже}' => $this->getNameInCaseInstrumental()
		);}
		return $this->{__METHOD__};
	}

	/**
	 * @param string $key
	 * @param bool $canBeTest[optional]
	 * @param string $defaultValue[optional]
	 * @return string
	 */
	public function getConst($key, $canBeTest = true, $defaultValue = '') {
		df_param_string($key, 0);
		df_param_boolean($canBeTest, 1);
		df_param_string($defaultValue, 2);
		/** @var string $result */
		$result = $this->getRmConfig()->getConst($key, $canBeTest, $defaultValue);
		return $result;
	}

	/**
	 * @param string $key
	 * @param bool $canBeTest[optional]
	 * @param string $defaultValue[optional]
	 * @return string
	 */
	public function getConstUrl($key, $canBeTest = true, $defaultValue = '') {
		df_param_string($key, 0);
		df_param_boolean($canBeTest, 1);
		df_param_string($defaultValue, 2);
		/** @var string $result */
		$result = $this->getRmConfig()->getConstManager()->getUrl($key, $canBeTest, $defaultValue);
		df_result_string($result);
		return $result;
	}

	/**
	 * Этот метод вызывается только одним методом:
	 * @see Mage_Payment_Helper_Data::getMethodFormBlock()
	 * @override
	 * @return string
	 */
	public function getFormBlockType() {
		return
			Df_Core_Model_ClassManager::s()->getResourceClass(
				$caller = $this
				,$resourceSuffix = 'Block_Form'
				,$defaultResult = Df_Payment_Block_Form::_CLASS
			)
		;
	}

	/**
	 * Этот метод вызывается только одним методом:
	 * @see Mage_Payment_Helper_Data::getInfoBlock()
	 * @override
	 * @return string
	 */
	public function getInfoBlockType() {
		return
			Df_Core_Model_ClassManager::s()->getResourceClass(
				$caller = $this
				,$resourceSuffix = 'Block_Info'
				,$defaultResult = Df_Payment_Block_Info::_CLASS
			)
		;
	}

	/**
	 * @param int|string|null|Mage_Core_Model_Store $storeId[optional]
	 * @return Df_Payment_Model_Config_Facade
	 */
	public function getRmConfig($storeId = null) {
		if (!is_int($storeId)) {
			$storeId =
				rm_nat0(
					is_null($storeId)
					? $this->getRmStore()->getId()
					: Mage::app()->getStore($storeId)->getId()
				)
			;
		}
		if (!isset($this->{__METHOD__}[$storeId])) {
			/** @var Df_Payment_Model_Config_Facade $result */
			$result =
				df_model(
					$this->getRmConfigClass()
					,array(
						Df_Payment_Model_Config_Facade::P__CONST_MANAGER =>
							Df_Payment_Model_ConfigManager_Const::i($this, Mage::app()->getStore($storeId))
						,Df_Payment_Model_Config_Facade::P__VAR_MANAGER =>
						Df_Payment_Model_ConfigManager_Var::i($this, Mage::app()->getStore($storeId))
						,Df_Payment_Model_Config_Facade::P__CONFIG_CLASS__ADMIN =>
							$this->getConfigClassAdmin()
						,Df_Payment_Model_Config_Facade::P__CONFIG_CLASS__FRONTEND =>
							$this->getConfigClassFrontend()
						,Df_Payment_Model_Config_Facade::P__CONFIG_CLASS__SERVICE =>
							$this->getConfigClassService()
					)
				)
			;
			df_assert($result instanceof Df_Payment_Model_Config_Facade);
			$this->{__METHOD__}[$storeId] = $result;
		}
		return $this->{__METHOD__}[$storeId];
	}

	/**
	 * Возвращает идентификатор способа оплаты внутри Российской сборки
	 * (без приставки «df-»)
	 * Этот метод публичен, потому что использутся классами:
	 * @see Df_Payment_Model_ConfigManager_Const
	 * @see Df_Payment_Model_ConfigManager_Var
	 * @return string
	 */
	public function getRmId() {return Df_Core_Model_ClassManager::s()->getFeatureCode($this);}

	/**
	 * Используется для того, чтобы предложить покупателю на странице оформления заказа
	 * меню из вариантов оплаты, предоставляемых одним и тем же платёжным шлюзом.
	 * Пока эта возможность используется только модулем LiqPay:
	 * @see Df_LiqPay_Model_Request_Payment::getParamsForXml()
	 * @todo Надо бы задействовать эту возможность и для других платёжных модулей,
	 * особенно для тех, которые работают с платёжными агрегаторами
	 * (потому что уж они то заведомо предоставляют несколько вариантов оплаты).
	 * @return string|null
	 */
	public function getSubmethod() {
		/** @var string|null $result */
		$result = $this->getInfoInstance()->getAdditionalInformation(self::INFO_KEY__SUBMETHOD);
		if (!is_null($result)) {
			df_result_string($result);
		}
		return $result;
	}

	/** @return string */
	public function getTemplateSuccess() {return '';}

	/**
	 * @override
	 * @return string
	 */
	public function getTitle() {return $this->getRmConfig()->frontend()->getTitle();}

	/**
	 * @param Mage_Sales_Model_Quote|null $quote[optional]
	 * @return bool
	 */
	public function isAvailable($quote = null) {
		return
				parent::isAvailable($quote)
			&&
				df_enabled($this->getRmFeatureCode(), $this->getRmStore())
		;
	}

	/**
	 * Насколько я понял, isGateway должно возвращать true,
	 * если процесс оплаты должен происходить непосредсвенно на странице оформления заказа,
	 * без перенаправления на страницу платёжной системы.
	 * В Российской сборке Magento так пока работает только метод @see Df_Chronopay_Model_Gate,
	 * однако он изготовлен давно и по устаревшей технологии,
	 * и поэтому не является наследником класса @see Df_Payment_Model_Method_Base
	 * @override
	 * @return bool
	 */
	public function isGateway() {
		return false;
	}

	/**
	 * Работает ли модуль в тестовом режиме?
	 * Обратите внимание, что если в настройках отсутствует ключ «test»,
	 * то модуль будет всегда находиться в рабочем режиме.
	 * @return bool
	 */
	public function isTestMode() {
		return $this->getRmConfig()->service()->isTestMode();
	}

	/**
	 * @param string|Exception $message
	 * @return Df_Payment_Model_Method_Base
	 */
	public function logFailureHighLevel($message) {
		if (is_string($message)) {
			/**
			 * Обратите внимание,
			 * что функция @see func_get_args() не может быть параметром другой функции.
			 */
			$arguments = func_get_args();
			$message = rm_sprintf($arguments);
		}
		$this->log($message, $filename = $this->getFailureLogFileNameHighLevel());
		return $this;
	}

	/**
	 * @param string|Exception $message
	 * @return Df_Payment_Model_Method_Base
	 */
	public function logFailureLowLevel($message) {
		if (is_string($message)) {
			/**
			 * Обратите внимание,
			 * что функция @see func_get_args() не может быть параметром другой функции.
			 */
			$arguments = func_get_args();
			$message = rm_sprintf($arguments);
		}
		$this->log($message, $filename = $this->getFailureLogFileNameLowLevel());
		return $this;
	}

	/**
	 * Этот метод вызывается только из метода @see Mage_Sales_Model_Order_Payment::refund().
	 * Обратите внимание, на реальные типы аргументов:
	 * аргумент $payment — это всегда объект класса Mage_Sales_Model_Order_Payment.
	 * аргумент $amount — это вовсе не с float, как описано в базовом классе, а строка:
	 * @see Mage_Sales_Model_Order_Payment::refund():
			$baseAmountToRefund = $this->_formatAmount($creditmemo->getBaseGrandTotal());
	 * @see Mage_Sales_Model_Order_Payment::_formatAmount():
		protected function _formatAmount($amount, $asFloat = false) {
		  $amount = Mage::app()->getStore()->roundPrice($amount);
		  return !$asFloat ? (string)$amount : $amount;
		}
	 * Т.к. метод @see Mage_Sales_Model_Order_Payment::refund() вызывает метод
	 * @see Mage_Sales_Model_Order_Payment::_formatAmount() без второго аргумента,
	 * то результатом вызова _formatAmount() будет именно строка.
	 *
	 * Обратите внимание, что размерностью $amount является не валюта заказа,
	 * а учётная валюта магазина:
	 * @see Mage_Sales_Model_Order_Payment::capture():
			$amountToCapture = $this->_formatAmount($invoice->getBaseGrandTotal());
	 *
	 * @override
	 * @param Varien_Object $payment
	 * @param string $amount
	 * @return Df_Payment_Model_Method_Base
	 */
	public function refund(Varien_Object $payment, $amount) {
		/** @var Mage_Sales_Model_Order_Payment $payment */
		/** @var string $amount */
		df_assert($payment instanceof Mage_Sales_Model_Order_Payment);
		/**
		 * @see Mage_Payment_Model_Method_Abstract::refund()
		 * контролирует допустимость вызова метода refund():
		 * если способ оплаты не поддерживает возврат средств покупателю
		 * (@see Df_Payment_Model_Method_Base::canRefund()),
		 * то Mage_Payment_Model_Method_Abstract::refund() возбудит исключительную ситуацию.
		 */
		parent::refund($payment, $amount);
		$this->doTransaction($payment, $amount, $handlerSuffix = 'Model_Refunder');
		return $this;
	}

	/**
	 * Этот метод вызывается только из метода @see Mage_Sales_Model_Order_Invoice::void().
	 * @override
	 * @param Mage_Sales_Model_Order_Payment|Varien_Object $payment
	 * @return Df_Payment_Model_Method_Base
	 */
	public function void(Varien_Object $payment) {
		parent::void($payment);
		$this->doTransaction($payment, 0, $handlerSuffix = 'Model_Voider');
		return $this;
	}

	/** @return string[] */
	protected function getCustomInformationKeys() {
		return array(self::INFO_KEY__SUBMETHOD);
	}

	/** @return string */
	protected function getRmConfigClass() {
		return
			Df_Core_Model_ClassManager::s()->getResourceClass(
				$caller = $this
				,$resourceSuffix = 'Model_Config_Facade'
				,$defaultResult = Df_Payment_Model_Config_Facade::_CLASS
			)
		;
	}

	/** @return string */
	protected function getRmFeatureCode() {
		return Df_Core_Model_ClassManager::s()->getFeatureCode($this);
	}

	/** @return Mage_Core_Model_Store */
	protected function getRmStore() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Mage::app()->getStore($this->getDataUsingMethod(self::P__STORE));
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param Mage_Sales_Model_Order_Payment $payment
	 * @param float|string $amount
	 * @param string $handlerSuffix
	 * @return Df_Payment_Model_Method_Base
	 */
	private function doTransaction(Mage_Sales_Model_Order_Payment $payment, $amount, $handlerSuffix) {
		/** @var string $handlerClass */
		$handlerClass =
			Df_Core_Model_ClassManager::s()->getResourceClass(
				$caller = $this
				,$resourceSuffix = $handlerSuffix
			)
		;
		/** @var Df_Payment_Model_Handler $handler */
		$handler =
			df_model(
				$handlerClass
				,array(
					Df_Payment_Model_Handler::P__AMOUNT => rm_float($amount)
					,Df_Payment_Model_Handler::P__METHOD => $this
					,Df_Payment_Model_Handler::P__ORDER_PAYMENT => $payment
				)
			)
		;
		df_assert($handler instanceof Df_Payment_Model_Handler);
		$handler->handle();
		return $this;
	}

	/** @return string */
	private function getConfigClassAdmin() {
		return
			Df_Core_Model_ClassManager::s()->getResourceClass(
				$caller = $this
				,$resourceSuffix = 'Model_Config_Area_Admin'
				,$defaultResult = Df_Payment_Model_Config_Area_Admin::_CLASS
			)
		;
	}

	/** @return string */
	private function getConfigClassFrontend() {
		return
			Df_Core_Model_ClassManager::s()->getResourceClass(
				$caller = $this
				,$resourceSuffix = 'Model_Config_Area_Frontend'
				,$defaultResult = Df_Payment_Model_Config_Area_Frontend::_CLASS
			)
		;
	}

	/** @return string */
	private function getConfigClassService() {
		return
			Df_Core_Model_ClassManager::s()->getResourceClass(
				$caller = $this
				,$resourceSuffix = 'Model_Config_Area_Service'
				,$defaultResult = Df_Payment_Model_Config_Area_Service::_CLASS
			)
		;
	}

	/** @return string */
	private function getFailureLogFileNameHighLevel() {
		return rm_sprintf('rm.%s.failure.highLevel.log', Df_Core_Model_ClassManager::s()->getFeatureCode($this));
	}

	/** @return string */
	private function getFailureLogFileNameLowLevel() {
		return rm_sprintf('rm.%s.failure.lowLevel.log', Df_Core_Model_ClassManager::s()->getFeatureCode($this));
	}

	/** @return string */
	private function getNameInCaseDative() {
		return $this->getConst('names/dative', $canBeTest = false, $default = $this->getTitle());
	}

	/** @return string */
	private function getNameInCaseGenitive() {
		return $this->getConst('names/genitive', $canBeTest = false, $default = $this->getTitle());
	}

	/** @return string */
	private function getNameInCaseInstrumental() {
		return $this->getConst('names/instrumental', $canBeTest = false, $default = $this->getTitle());
	}

	/** @return string */
	private function getNameInNominativeCase() {
		return $this->getConst('names/nominative', $canBeTest = false, $default = $this->getTitle());
	}

	/**
	 * @param string|Exception $message
	 * @param string $filename
	 * @return Df_Payment_Model_Method_Base
	 */
	private function log($message, $filename) {
		if ($message instanceof Exception) {
			Df_Qa_Message_Failure_Exception::i(array(
				Df_Qa_Message_Failure_Exception::P__EXCEPTION => $message
				,Df_Qa_Message_Failure_Exception::P__FILE_NAME => $filename
				,Df_Qa_Message_Failure_Exception::P__NEED_LOG_TO_FILE => true
				,Df_Qa_Message_Failure_Exception::P__NEED_NOTIFY_DEVELOPER => true
			))->log();
		}
		else if (is_string($message)) {
			Df_Qa_Message_Notification::i(array(
				Df_Qa_Message_Notification::P__NOTIFICATION => $message
				,Df_Qa_Message_Notification::P__NEED_LOG_TO_FILE => true
				,Df_Qa_Message_Notification::P__FILE_NAME => $filename
				,Df_Qa_Message_Notification::P__NEED_NOTIFY_DEVELOPER => true
			))->log();
		}
		else {
			df_error();
		}
		return $this;
	}
	const _CLASS = __CLASS__;
	const INFO_KEY__SUBMETHOD = 'df_payment__submethod';
	const P__STORE = 'store';
	const RM__ID_PREFIX = 'df';
	const RM__ID_SEPARATOR = '-';
	/**
	 * @static
	 * @param string $rmId
	 * @return string
	 */
	public static function getCodeByRmId($rmId) {
		df_param_string($rmId, 0);
		return implode(self::RM__ID_SEPARATOR, array(self::RM__ID_PREFIX, $rmId));
	}
}