<?php
abstract class Df_IPay_Model_Action_Abstract extends Df_Payment_Model_Action_Abstract {
	/**
	 * @abstract
	 * @return string
	 */
	abstract protected function getExpectedRequestType();

	/**
	 * @abstract
	 * @return string
	 */
	abstract protected function getRequestAsXml_Test();

	/**
	 * @abstract
	 * @return Df_IPay_Model_Action_Abstract
	 */
	abstract protected function processInternal();

	/**
	 * @override
	 * @return Df_IPay_Model_Action_Abstract
	 */
	public function process() {
		try {
			$this->checkSignature();
			$this->checkRequestType();
			$this->checkProtocolVersion();
			$this->checkCurrencyId();
			$this->checkOrderState();
			$this->checkOrderPaymentMethod();
			$this->checkTransactionState();
			$this->getTransactionState()->update($this->getRequestParam_RequestType());
			$this->processInternal();
		}
		catch(Exception $e) {
			try {
				$this->getTransactionState()->restore();
			}
			catch(Exception $e) {
				/**
				 * Дополнительные сбои нас уже не интересуют.
				 */
			}
			$this->processException($e);
			df_handle_entry_point_exception($e, false);
		}
		rm_response_content_type($this->getResponse(), 'text/xml');
		$this->getResponse()
			->setHeader( self::HEADER__SIGNATURE, $this->getResponseHeader_Signature())
			->setBody($this->getResponseAsXml())
		;
		return $this;
	}

	/**
	 * @param string $configKey
	 * @return string
	 */
	protected function getMessage($configKey) {
		df_param_string($configKey, 0);
		return str_replace('<br/>', "\n", $this->getConst($configKey));
	}

	/**
	 * @override
	 * @return Df_Sales_Model_Order
	 */
	protected function getOrder() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Sales_Model_Order $result */
			$result = Df_Sales_Model_Order::i();
			$result->load($this->getOrderId());
			if (
					rm_nat0($result->getId()) !== $this->getOrderId()
				||
					(0 === rm_nat0($result->getId()))
			) {
				df_error(
					'Заказ номер %d не существует. Начните оплату заново с сайта %s'
					,$this->getOrderId()
					,Zend_Uri_Http::fromString(
						Mage::app()->getStore()->getBaseUrl(
							Mage_Core_Model_Store::URL_TYPE_WEB
						)
					)->getHost()
				);
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}
	
	/** @return Mage_Sales_Model_Order_Payment */
	protected function getPayment() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->getOrder()->getPayment();
			/** Mage_Sales_Model_Order::getPayment может иногда возвращать false */
			df_assert($this->{__METHOD__} instanceof Mage_Sales_Model_Order_Payment);
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return Df_IPay_Model_Payment
	 */
	protected function getPaymentMethod() {
		/** @var Df_IPay_Model_Payment $result */
		$result = null;
		try {
			$result = parent::getPaymentMethod();
		}
		catch(Exception $e) {
			// Сюда мы попадаем, например, когда нам нужно сформировать цифровую подпись
			// для ответа о несуществующем заказе
			$result = Df_IPay_Model_Payment::s();
		}
		return $result;
	}

	/** @return Df_IPay_Model_Config_Area_Service */
	protected function getServiceConfig() {return $this->getRmConfig()->service();}

	/** @return array(string => string) */
	protected function getRequestAsCanonicalArray() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_xml($this->getRequestAsXml())->asCanonicalArray();
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param string $paramName
	 * @param string|null $defaultValue
	 * @return string
	 */
	protected function getRequestParam($paramName, $defaultValue = null) {
		df_param_string($paramName, 0);
		/** @var string|null $result */
		$result = df_array_query($this->getRequestAsCanonicalArray(), $paramName, $defaultValue);
		if (!is_null($result)) {
			df_result_string($result);
		}
		return $result;
	}

	/** @return Df_IPay_Model_Request_Payment */
	protected function getRequestPayment() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_IPay_Model_Request_Payment::i($this->getOrder());
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Varien_Simplexml_Element */
	protected function getResponseAsSimpleXmlElement() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				rm_xml(
					/**
					 * Обратите внимание, что хотя iPay ожидает документ в кодировке windows-1251,
					 * здесь, в Magento, документ надо создавать именно в кодировке utf-8,
					 * потому что иначе при попытке добавить к документу в кодировке windows-1251
					 * элементы в кодировке utf-8 (а это кодировка файлов программного кода),
					 * SimpleXml вызовет исключительную ситуацию:
					 * "parser error : switching encoding: encoder error"
					 */
					"<?xml version='1.0' encoding='utf-8'?>"
					."<ServiceProvider_Response></ServiceProvider_Response>"
				)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return Mage_Core_Model_Store */
	protected function getStore() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				(0 === $this->getOrderId())
				? Mage::app()->getStore()
				: $this->getOrder()->getStore()
			;
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	protected function getStoreDomain() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->getStoreUri()->getHost();
			/** @see Zend_Uri_Http::getHost может вернуть false. */
			df_result_string($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/** @return Zend_Uri_Http */
	protected function getStoreUri() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Zend_Uri_Http::fromString(
				$this->getStore()->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB)
			);
		}
		return $this->{__METHOD__};
	}

	/** @return Df_IPay_Model_TransactionState */
	protected function getTransactionState() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_IPay_Model_TransactionState::i($this->getPayment());
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function calculateRequestSignature() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = strtoupper(md5(df_concat(
				$this->getRmConfig()->service()->getResponsePassword()
				,$this->preprocessXmlForSignature($this->getRequestAsXmlInWindows1251())
			)));
		}
		return $this->{__METHOD__};
	}

	/**
	 * Обратите внимание, что не при всяком запросе
	 * iPay присылает идентификатор магазина
	 * @return Df_IPay_Model_Action_Abstract
	 */
	private function checkCurrencyId() {
		if (self::EXPECTED_CURRENCY_ID !== $this->getRequestParam_CurrencyId()) {
			df_error(
				'Модуль iPay запрограммирован для работы с валютой «%d»,'
				.' однако платёжная система iPay пометила запрос непредусмотренной валютой «%d».'
				,self::EXPECTED_CURRENCY_ID
				,$this->getRequestParam_CurrencyId()
			);
		}
		return $this;
	}

	/** @return Df_IPay_Model_Action_Abstract */
	private function checkOrderState() {
		if ($this->getOrder()->canUnhold()) {
			$this->logOrderMessage(
				'Заказ номер %orderId% не предназначен для оплаты, потому что он заморожен.'
			);
			$this->throwOrderNotExists();
		}
		if ($this->getOrder()->isPaymentReview()) {
			$this->logOrderMessage(
				'Заказ номер %orderId% не предназначен для оплаты,'
				. ' потому что находится на модерации оплаты.'
			);
			$this->throwOrderNotExists();
		}
		if ($this->getOrder()->isCanceled()) {
			$this->logOrderMessage(
				'Заказ номер %orderId% не предназначен для оплаты, потому что он отменён.'
			);
			$this->throwOrderNotExists();
		}
		if (Mage_Sales_Model_Order::STATE_COMPLETE === $this->getOrder()->getState()) {
			$this->logOrderMessage(
				'Заказ номер %orderId% не предназначен для оплаты, потому что он выполнен.'
			);
			$this->throwOrderAlreadyPayed();
		}
		if (Mage_Sales_Model_Order::STATE_CLOSED === $this->getOrder()->getState()) {
			$this->logOrderMessage(
				'Заказ номер %orderId% не предназначен для оплаты, потому что он закрыт.'
			);
			$this->throwOrderAlreadyPayed();
		}
		if (
				false
			===
				$this->getOrder()->getActionFlag(Mage_Sales_Model_Order::ACTION_FLAG_INVOICE)
		) {
			$this->logOrderMessage(
				'Заказ номер %orderId% помечен системой как непредназначенный для оплаты.'
			);
			$this->throwOrderNotExists();
		}
		$hasQtyYoInvoice = false;
		foreach ($this->getOrder()->getAllItems() as $item) {
			/** @var Mage_Sales_Model_Order_Item $item */
			if (0 < $item->getQtyToInvoice() && !$item->getLockedDoInvoice()) {
				$hasQtyYoInvoice = true;
				break;
			}
		}
		if (!$hasQtyYoInvoice) {
			/** @var Mage_Sales_Model_Order_Payment|bool $payment */
			$payment = $this->getOrder()->getPayment();
			if (false === $payment) {
				$this->logOrderMessage('Заказ номер %orderId% не предназначен для оплаты.');
				$this->throwOrderNotExists();
			}
			else {
				df_assert($payment instanceof Mage_Sales_Model_Order_Payment);
				/** @var Mage_Payment_Model_Method_Abstract $paymentMethod */
				$paymentMethod = $payment->getMethodInstance();
				df_assert($paymentMethod instanceof Mage_Payment_Model_Method_Abstract);
				if (!($paymentMethod instanceof Df_IPay_Model_Payment)) {
					$this->logOrderMessage(
						'Заказ номер %orderId% не предназначен для оплаты посредством iPay.'
					);
					$this->throwOrderNotExists();
				}
				else {
					if (0.0 === rm_float($this->getOrder()->getBaseGrandTotal())) {
						$this->logOrderMessage(
							'Заказ номер %orderId% не предназначен для оплаты,'
							.' потому что он бесплатен.'
						);
						$this->throwOrderNotExists();
					}
					else {
						$this->throwOrderAlreadyPayed();
					}
				}
			}
		}
		return $this;
	}

	/** @return Df_IPay_Model_Action_Abstract */
	private function checkProtocolVersion() {
		if (self::EXPECTED_PROTOCOL_VERSION !== $this->getRequestParam_ProtocolVersion()) {
			df_error(
				'Модуль iPay запрограммирован для работы с протоколом iPay версии «%s»,'
				. ' однако платёжная система iPay прислала запрос,'
				. ' помеченный версией протокола «%s».'
				,self::EXPECTED_PROTOCOL_VERSION
				,$this->getRequestParam_ProtocolVersion()
			);
		}
		return $this;
	}

	/** @return Df_IPay_Model_Action_Abstract */
	private function checkOrderPaymentMethod() {
		if (!($this->getOrder()->getPayment()->getMethodInstance() instanceof Df_IPay_Model_Payment)) {
			$this->throwOrderNotExists();
		}
		return $this;
	}

	/** @return Df_IPay_Model_Action_Abstract */
	private function checkRequestType() {
		if ($this->getRequestParam_RequestType() !== $this->getExpectedRequestType())  {
			df_error(
				'Класс «%s» предназначен для обработки запросов типа «%s»,однако платёжная система iPay прислала ему запрос типа «%s».'
				,get_class($this)
				,$this->getExpectedRequestType()
				,$this->getRequestParam_RequestType()
			);
		}
		return $this;
	}

	/** @return Df_IPay_Model_Action_Abstract */
	private function checkSignature() {
		if (!df_is_it_my_local_pc()) {
			if ($this->getRequestParam_Signature() !== $this->calculateRequestSignature()) {
				df_error(
					"Запрос от iPay подписан неверно.
					\nОжидаемая подпись: «%s».
					\nПолученная подпись: «%s»."
					,$this->calculateRequestSignature()
					,$this->getRequestParam_Signature()
				);
			}
		}
		return $this;
	}

	/** @return Df_IPay_Model_Action_Abstract */
	private function checkTransactionState() {
		if (
				(self::TRANSACTION_STATE__START === $this->getTransactionState()->get())
			&&
				in_array(
					$this->getRequestParam_RequestType()
					,array(
						self::TRANSACTION_STATE__SERVICE_INFO
						,self::TRANSACTION_STATE__START
					)
				)
		) {
			df_error('Заказ номер %d находится в процессе оплаты', $this->getOrder()->getId());
		}
		return $this;
	}

	/** @return string */
	private function generateResponseSignature() {
		/** @var string $result */
		$result =
			strtoupper(
				md5(
					df_concat(
						$this->getRmConfig()->service()->getResponsePassword()
						,$this->preprocessXmlForSignature(
							df_text()->convertUtf8ToWindows1251($this->getResponseAsXml())
						)
					)
				)
			)
		;
		return $result;
	}

	/** @return int */
	private function getOrderId() {return rm_nat0($this->getRequestParam(self::REQUEST_PARAM__ORDER_ID));}

	/** @return string */
	private function getRequestAsXml() {
		return df_text()->convertWindows1251ToUtf8($this->getRequestAsXmlInWindows1251());
	}

	/** @return string */
	private function getRequestAsXmlInWindows1251() {
		/** @var string $result */
		$result =
			df_is_it_my_local_pc()
			? $this->getRequestAsXml_Test()
			: $this->getRequest()->getParam('XML')
		;
		df_result_string($result);
		return $result;
	}

	/** @return string */
	private function getRequestHeader_Signature() {
		/** @var string $result */
		$result = $this->getController()->getRequest()->getHeader(self::HEADER__SIGNATURE);
		df_result_string($result);
		return $result;
	}

	/** @return int */
	private function getRequestParam_CurrencyId() {
		return rm_nat($this->getRequestParam(self::REQUEST_PARAM__CURRENCY_ID));
	}

	/** @return string */
	private function getRequestParam_Language() {
		/** @var string $result */
		$result = $this->getRequestParam(self::REQUEST_PARAM__LANGUAGE);
		df_result_string($result);
		return $result;
	}

	/** @return string */
	private function getRequestParam_ProtocolVersion() {
		/** @var string $result */
		$result = $this->getRequestParam(self::REQUEST_PARAM__PROTOCOL_VERSION);
		df_result_string($result);
		return $result;
	}

	/** @return string */
	private function getRequestParam_RequestType() {
		/** @var string $result */
		$result = $this->getRequestParam(self::REQUEST_PARAM__TYPE);
		df_result_string($result);
		return $result;
	}

	/** @return int */
	private function getRequestParam_ShopId() {
		return rm_nat($this->getRequestParam(self::REQUEST_PARAM__SHOP_ID));
	}

	/** @return string */
	private function getRequestParam_Signature() {
		if (!isset($this->{__METHOD__})) {
			/** @var string[] $signatureHeaderAsArray */
			$signatureHeaderAsArray = df_trim(explode(':', $this->getRequestHeader_Signature()));
			/** @var string $signatureType */
			$signatureType = df_a($signatureHeaderAsArray, 0);
			df_assert_string($signatureType);
			if (self::SIGNATYPE_TYPE !== $signatureType) {
				df_error(
					'Модуль ожидает «%s» в качестве типа цифровой подписи,'
					.' однако подпись от iPay имеет тип «%s».'
					,self::SIGNATYPE_TYPE
					,$signatureType
				);
			}
			/** @var string $result */
			$result = df_a($signatureHeaderAsArray, 1);
			df_result_string($result);
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return int */
	private function getRequestParam_TransactionId() {
		return rm_nat($this->getRequestParam(self::REQUEST_PARAM__TRANSACTION_ID));
	}

	/** @return string */
	private function getResponseAsXml() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $result */
			// Похоже, нам не надо здесь вызывать df_text()->convertUtf8ToWindows1251,
			// потому что библиотека SimpleXml перекодирует текст автоматически
			// в ту кодировку, которая указана в заголовке XML.
			$result = $this->getResponseAsSimpleXmlElement()->asXml();
			$this->{__METHOD__} = str_replace('utf-8', 'windows-1251', $result);
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getResponseHeader_Signature() {
		return df_concat(self::SIGNATYPE_TYPE, ': ', $this->generateResponseSignature());
	}

	/**
	 * @param string $message
	 * @return Df_IPay_Model_Action_Abstract
	 */
	private function logOrderMessage($message) {
		df_notify(str_replace('%orderId%', $this->getOrder()->getId(), $message));
		df_bt();
		return $this;
	}

	/** @return Df_Payment_Model_Config_Facade */
	private function getRmConfig() {
		if (!isset($this->{__METHOD__})) {
			/** @var Mage_Core_Model_Store $store */
			try {
				$store = $this->getOrder()->getStore();
			}
			catch(Exception $e) {
				$store = Mage::app()->getStore();
			}
			$this->{__METHOD__} = Df_IPay_Model_Payment::i()->getRmConfig($store);
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param string $xml
	 * @return string
	 */
	private function preprocessXmlForSignature($xml) {
		return str_replace("\r\n", "\n", trim($xml));
	}

	/**
	 * @param Exception $e
	 * @return Df_IPay_Model_Action_Abstract
	 */
	private function processException(Exception $e) {
		$this->getResponseAsSimpleXmlElement()->appendChild(
			Df_Varien_Simplexml_Element::createNode('Error')->appendChild(
				Df_Varien_Simplexml_Element::createNode('ErrorLine')->setCData(rm_ets($e))
			)
		);
		return $this;
	}

	/** @return Df_IPay_Model_Action_Abstract */
	private function throwOrderAlreadyPayed() {
		df_error('Заказ номер %d уже оплачен', $this->getOrder()->getId());
		return $this;
	}

	/** @return Df_IPay_Model_Action_Abstract */
	private function throwOrderNotExists() {
		df_error(
			'Заказ номер %d не существует. Начните оплату заново с сайта %s'
			,$this->getOrder()->getId()
			,$this->getStoreDomain()
		);
		return $this;
	}

	const _CLASS = __CLASS__;
	const EXPECTED_CURRENCY_ID = 974;
	const EXPECTED_PROTOCOL_VERSION = '1';
	const HEADER__SIGNATURE = 'ServiceProvider-Signature';
	const MESSAGE__ORDER_NOT_FOUND =
		'Платёжная система iPay прислала сообщение «%s»
		относительно несуществующего заказа с идентификатором «%d».'
	;
	const REQUEST_PARAM__CURRENCY_ID = 'Currency';
	const REQUEST_PARAM__LANGUAGE = 'Language';
	const REQUEST_PARAM__ORDER_ID = 'PersonalAccount';
	const REQUEST_PARAM__PROTOCOL_VERSION = 'Version';
	const REQUEST_PARAM__SHOP_ID = 'ServiceNo';
	const REQUEST_PARAM__TRANSACTION_ID = 'RequestId';
	const REQUEST_PARAM__TYPE = 'RequestType';
	const PAYMENT_PARAM__IPAY_TRANSACTION_STATE = 'df_ipay__transaction_state';
	const SIGNATYPE_TYPE = 'SALT+MD5';
	const TRANSACTION_STATE__RESULT = 'TransactionResult';
	const TRANSACTION_STATE__SERVICE_INFO = 'ServiceInfo';
	const TRANSACTION_STATE__START = 'TransactionStart';
}