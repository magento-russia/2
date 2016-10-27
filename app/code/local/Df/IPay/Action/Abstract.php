<?php
/** @method Df_IPay_Config_Area_Service configS() */
abstract class Df_IPay_Action_Abstract extends \Df\Payment\Action {
	/**
	 * @abstract
	 * @used-by checkRequestType()
	 * @return string
	 */
	abstract protected function getExpectedRequestType();

	/**
	 * @abstract
	 * @used-by getRequestAsXmlInWindows1251()
	 * @return string
	 */
	abstract protected function getRequestAsXml_Test();

	/**
	 * Обратите внимание, что хотя iPay ожидает документ в кодировке windows-1251,
	 * здесь, в Magento, документ надо создавать именно в кодировке utf-8,
	 * потому что иначе при попытке добавить к документу в кодировке windows-1251
	 * элементы в кодировке utf-8 (а это кодировка файлов программного кода),
	 * SimpleXml вызовет исключительную ситуацию:
	 * "parser error : switching encoding: encoder error"
	 * @used-by generateResponseBody()
	 * @used-by processException()
	 * @used-by Df_IPay_Action_Confirm::_process()
	 * @used-by Df_IPay_Action_ConfirmPaymentByShop::_process()
	 * @used-by Df_IPay_Action_GetPaymentAmount::_process()
	 * @return \Df\Xml\X
	 */
	protected function e() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_xml_parse(
				"<?xml version='1.0' encoding='utf-8'?>"
				."<ServiceProvider_Response></ServiceProvider_Response>"
			);
		}
		return $this->{__METHOD__};
	}

	/**
	 * Обратите внимание, что IPay требует именно «text/xml».
	 * @override
	 * @see Df_Core_Model_Action::contentType()
	 * @used-by Df_Core_Model_Action::getResponseLogFileExtension()
	 * @used-by Df_Core_Model_Action::processPrepare()
	 * @return string
	 */
	protected function contentType() {return 'text/xml';}

	/**
	 * Похоже, нам не надо здесь вызывать df_t()->convertUtf8ToWindows1251,
	 * потому что библиотека SimpleXml перекодирует текст автоматически
	 * в ту кодировку, которая указана в заголовке XML.
	 * @override
	 * @see Df_Core_Model_Action::generateResponseBody()
	 * @used-by Df_Core_Model_Action::responseBody()
	 * @return string
	 */
	protected function generateResponseBody() {
		return str_replace('utf-8', 'windows-1251', $this->e()->asXml());
	}

	/**
	 * @used-by Df_IPay_Action_Confirm::_process()
	 * @used-by Df_IPay_Action_ConfirmPaymentByShop::checkPaymentAmount()
	 * @param string $configKey
	 * @return string
	 */
	protected function getMessage($configKey) {
		return str_replace('<br/>', "\n", $this->const_($configKey));
	}

	/**
	 * @used-by getRequestParam_CurrencyId()
	 * @used-by getRequestParam_ShopId()
	 * @used-by getRequestParam_TransactionId()
	 * @used-by orderId()
	 * @used-by Df_IPay_Action_Confirm::getRequestParam_ErrorText()
	 * @used-by Df_IPay_Action_ConfirmPaymentByShop::getRequestParam_PaymentAmount()
	 * @param string $paramName
	 * @param string|null $defaultValue
	 * @return string|null
	 */
	protected function getRequestParam($paramName, $defaultValue = null) {
		return dfa_deep($this->getRequestA(), $paramName, $defaultValue);
	}

	/** @return Df_IPay_Request_Payment */
	protected function getRequestPayment() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_IPay_Request_Payment::i($this->order());
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
				$this->store()->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB)
			);
		}
		return $this->{__METHOD__};
	}

	/** @return Df_IPay_TransactionState */
	protected function getTransactionState() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_IPay_TransactionState::i($this->payment());
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @see \Df\Payment\Action::method()
	 * @used-by \Df\Payment\Action::getConst()
	 * @used-by \Df\Payment\Action::info()
	 * @return Df_IPay_Method
	 */
	protected function method() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_IPay_Method $result */
			try {
				$result = parent::method();
			}
			catch (Exception $e) {
				// Сюда мы попадаем, например, когда нам нужно сформировать цифровую подпись
				// для ответа о несуществующем заказе
				/** @var Df_Core_Model_StoreM $store */
				try {
					$store = $this->order()->getStore();
				}
				catch (Exception $e) {
					$store = df_store();
				}
				$result = Df_IPay_Method::i($store);
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return bool
	 */
	protected function needAddExceptionToSession() {return false;}

	/**
	 * @override
	 * @return bool
	 */
	protected function needRethrowException() {return false;}

	/**
	 * 2015-03-17
	 * Обратите внимание, что, в отличие от большинства остальных модулей оплаты
	 * @see \Df\Payment\Action\Confirm::order()
	 * в данном случае мы загружаем заказ не по increment_id, а по id.
	 * @override
	 * @see \Df\Payment\Action::order()
	 * @used-by \Df\Payment\Action::comment()
	 * @used-by \Df\Payment\Action::method()
	 * @used-by \Df\Payment\Action::payment()
	 * @used-by checkOrderState()
	 * @used-by checkTransactionState()
	 * @used-by config()
	 * @used-by getRequestPayment()
	 * @used-by store()
	 * @used-by logOrderMessage()
	 * @used-by throwOrderAlreadyPayed()
	 * @used-by throwOrderNotExists()
	 * @return Df_Sales_Model_Order
	 */
	protected function order() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Sales_Model_Order|null $result */
			$result = Df_Sales_Model_Order::ld($this->orderId(), null, false);
			if (!$result) {
				df_error(
					'Заказ номер %d не существует. Начните оплату заново с сайта %s'
					,$this->orderId()
					,df_current_domain()
				);
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return void
	 */
	protected function processBeforeRedirect() {
		$this->response()
			->setHeader(self::$HEADER__SIGNATURE, $this->getResponseHeader_Signature())
			->setBody($this->responseBody())
		;
	}

	/**
	 * @override
	 * @param Exception $e
	 * @return void
	 */
	protected function processException(Exception $e) {
		try {
			$this->getTransactionState()->restore();
		}
		// дополнительные сбои нас уже не интересуют
		catch (Exception $e) {}
		$this->e()->appendChild(
			df_xml_node('Error')->appendChild(df_xml_node('ErrorLine')->setCData(df_ets($e)))
		);
		parent::processException($e);
	}

	/**
	 * @override
	 * @return void
	 */
	protected function processPrepare() {
		parent::processPrepare();
		$this->checkSignature();
		$this->checkRequestType();
		$this->checkProtocolVersion();
		$this->checkCurrencyId();
		$this->checkOrderState();
		$this->checkOrderPaymentMethod();
		$this->checkTransactionState();
		$this->getTransactionState()->update($this->getRequestParam_RequestType());
	}

	/**
	 * @override
	 * @see Df_Core_Model_Action::store()
	 * @used-by Df_Core_Model_Action::checkAccessRights()
	 * @used-by Df_Core_Model_Action::getStoreConfig()
	 * @used-by getStoreUri()
	 * @return Df_Core_Model_StoreM
	 */
	protected function store() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = !$this->orderId() ? df_store() : $this->order()->getStore();
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function calculateRequestSignature() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = strtoupper(md5(df_c(
				$this->configS()->getRequestPassword()
				,$this->preprocessXmlForSignature($this->getRequestAsXmlInWindows1251())
			)));
		}
		return $this->{__METHOD__};
	}

	/**
	 * Обратите внимание, что не при всяком запросе
	 * iPay присылает идентификатор магазина
	 * @return void
	 */
	private function checkCurrencyId() {
		/** @var int $id */
		$id = 974;
		if ($id !== $this->getRequestParam_CurrencyId()) {
			df_error(
				'Модуль iPay запрограммирован для работы с валютой «%d»,'
				.' однако платёжная система iPay пометила запрос непредусмотренной валютой «%d».'
				, $id
				, $this->getRequestParam_CurrencyId()
			);
		}
	}

	/** @return void */
	private function checkOrderState() {
		if ($this->order()->canUnhold()) {
			$this->logOrderMessage(
				'Заказ номер %orderId% не предназначен для оплаты, потому что он заморожен.'
			);
			$this->throwOrderNotExists();
		}
		if ($this->order()->isPaymentReview()) {
			$this->logOrderMessage(
				'Заказ номер %orderId% не предназначен для оплаты,'
				. ' потому что находится на модерации оплаты.'
			);
			$this->throwOrderNotExists();
		}
		if ($this->order()->isCanceled()) {
			$this->logOrderMessage(
				'Заказ номер %orderId% не предназначен для оплаты, потому что он отменён.'
			);
			$this->throwOrderNotExists();
		}
		if (Mage_Sales_Model_Order::STATE_COMPLETE === $this->order()->getState()) {
			$this->logOrderMessage(
				'Заказ номер %orderId% не предназначен для оплаты, потому что он выполнен.'
			);
			$this->throwOrderAlreadyPayed();
		}
		if (Mage_Sales_Model_Order::STATE_CLOSED === $this->order()->getState()) {
			$this->logOrderMessage(
				'Заказ номер %orderId% не предназначен для оплаты, потому что он закрыт.'
			);
			$this->throwOrderAlreadyPayed();
		}
		if (
				false
			===
				$this->order()->getActionFlag(Mage_Sales_Model_Order::ACTION_FLAG_INVOICE)
		) {
			$this->logOrderMessage(
				'Заказ номер %orderId% помечен системой как непредназначенный для оплаты.'
			);
			$this->throwOrderNotExists();
		}
		$hasQtyYoInvoice = false;
		foreach ($this->order()->getAllItems() as $item) {
			/** @var Mage_Sales_Model_Order_Item $item */
			if (0 < $item->getQtyToInvoice() && !$item->getLockedDoInvoice()) {
				$hasQtyYoInvoice = true;
				break;
			}
		}
		if (!$hasQtyYoInvoice) {
			/** @var Mage_Sales_Model_Order_Payment $payment */
			try {
				$payment = $this->payment();
			}
			catch (Exception $e) {
				$this->logOrderMessage('Заказ номер %orderId% не предназначен для оплаты.');
				$this->throwOrderNotExists();
			}
			/** @var Mage_Payment_Model_Method_Abstract $paymentMethod */
			$paymentMethod = $payment->getMethodInstance();
			df_assert($paymentMethod instanceof Mage_Payment_Model_Method_Abstract);
			if (!($paymentMethod instanceof Df_IPay_Method)) {
				$this->logOrderMessage(
					'Заказ номер %orderId% не предназначен для оплаты посредством iPay.'
				);
				$this->throwOrderNotExists();
			}
			else {
				if (0.0 === df_float($this->order()->getBaseGrandTotal())) {
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

	/** @return void */
	private function checkProtocolVersion() {
		/** @var string $version */
		$version = '1';
		if ($version !== $this->getRequestParam_ProtocolVersion()) {
			df_error(
				'Модуль iPay запрограммирован для работы с протоколом iPay версии «%s»,'
				. ' однако платёжная система iPay прислала запрос,'
				. ' помеченный версией протокола «%s».'
				,$version
				,$this->getRequestParam_ProtocolVersion()
			);
		}
	}

	/** @return void */
	private function checkOrderPaymentMethod() {
		// надо писать именно так, а не $this->payment()
		if (!$this->payment()->getMethodInstance() instanceof Df_IPay_Method) {
			$this->throwOrderNotExists();
		}
	}

	/** @return void */
	private function checkRequestType() {
		if ($this->getRequestParam_RequestType() !== $this->getExpectedRequestType())  {
			df_error(
				'Класс «%s» предназначен для обработки запросов типа «%s»,'
				. ' однако платёжная система iPay прислала ему запрос типа «%s».'
				,get_class($this)
				,$this->getExpectedRequestType()
				,$this->getRequestParam_RequestType()
			);
		}
	}

	/** @return void */
	private function checkSignature() {
		if (!df_my_local()) {
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
	}

	/** @return void */
	private function checkTransactionState() {
		if (
				self::$TRANSACTION_STATE__START === $this->getTransactionState()->get()
			&&
				in_array($this->getRequestParam_RequestType(), array(
					self::$TRANSACTION_STATE__SERVICE_INFO, self::$TRANSACTION_STATE__START
				))
		) {
			df_error('Заказ номер %d находится в процессе оплаты', $this->orderId());
		}
	}

	/** @return string */
	private function generateResponseSignature() {
		return strtoupper(md5(df_c(
			$this->configS()->getResponsePassword()
			,$this->preprocessXmlForSignature(df_1251_to($this->responseBody()))
		)));
	}

	/**
	 * @used-by getRequestParam()
	 * @used-by getRequestParamR()
	 * @return array(string => mixed)
	 */
	private function getRequestA() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_xml_parse($this->getRequestAsXml())->asCanonicalArray();
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getRequestAsXml() {return df_1251_from($this->getRequestAsXmlInWindows1251());}

	/** @return string */
	private function getRequestAsXmlInWindows1251() {
		return df_my_local() ? $this->getRequestAsXml_Test() : $this->param('XML');
	}

	/** @return string */
	private function getRequestHeader_Signature() {
		return $this->request()->getHeader(self::$HEADER__SIGNATURE);
	}

	/** @return int */
	private function getRequestParam_CurrencyId() {return df_nat($this->getRequestParam('Currency'));}

	/** @return string */
	private function getRequestParam_Language() {return $this->getRequestParamR('Language');}

	/** @return string */
	private function getRequestParam_ProtocolVersion() {return $this->getRequestParamR('Version');}

	/** @return string */
	private function getRequestParam_RequestType() {return $this->getRequestParamR('RequestType');}

	/** @return int */
	private function getRequestParam_ShopId() {return df_nat($this->getRequestParam('ServiceNo'));}

	/** @return string */
	private function getRequestParam_Signature() {
		if (!isset($this->{__METHOD__})) {
			/** @var string[] $signatureHeaderAsArray */
			$signatureHeaderAsArray = df_trim(explode(':', $this->getRequestHeader_Signature()));
			/** @var string $signatureType */
			$signatureType = dfa($signatureHeaderAsArray, 0);
			df_assert_string($signatureType);
			if (self::$SIGNATYPE_TYPE !== $signatureType) {
				df_error(
					'Модуль ожидает «%s» в качестве типа цифровой подписи,'
					.' однако подпись от iPay имеет тип «%s».'
					,self::$SIGNATYPE_TYPE
					,$signatureType
				);
			}
			/** @var string $result */
			$result = dfa($signatureHeaderAsArray, 1);
			df_result_string($result);
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return int */
	private function getRequestParam_TransactionId() {
		return df_nat($this->getRequestParam('RequestId'));
	}

	/**
	 * @used-by getRequestParam_Language()
	 * @used-by getRequestParam_ProtocolVersion()
	 * @used-by getRequestParam_RequestType()
	 * @param string $paramName
	 * @param string|null $defaultValue
	 * @return string
	 */
	private function getRequestParamR($paramName, $defaultValue = null) {
		df_param_string_not_empty($paramName, 0);
		/** @var string|null $result */
		$result = dfa_deep($this->getRequestA(), $paramName, $defaultValue);
		df_result_string_not_empty($result);
		return $result;
	}

	/** @return string */
	private function getResponseHeader_Signature() {
		return df_c(self::$SIGNATYPE_TYPE, ': ', $this->generateResponseSignature());
	}

	/**
	 * @param string $message
	 * @return void
	 */
	private function logOrderMessage($message) {
		df_notify(str_replace('%orderId%', $this->orderId(), $message));
		df_bt();
	}

	/**
	 * @used-by checkTransactionState()
	 * @used-by logOrderMessage()
	 * @used-by order()
	 * @used-by store()
	 * @used-by throwOrderAlreadyPayed()
	 * @used-by throwOrderNotExists()
	 * @return int
	 */
	private function orderId() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_nat0($this->getRequestParam('PersonalAccount'));
		}
		return $this->{__METHOD__};
	}


	/**
	 * @param string $xml
	 * @return string
	 */
	private function preprocessXmlForSignature($xml) {return str_replace("\r\n", "\n", trim($xml));}

	/** @return void */
	private function throwOrderAlreadyPayed() {df_error('Заказ номер %d уже оплачен', $this->orderId());}

	/** @return void */
	private function throwOrderNotExists() {
		df_error(
			'Заказ номер %d не существует. Начните оплату заново с сайта %s'
			,$this->orderId()
			,$this->getStoreDomain()
		);
	}
	/**
	 * @used-by checkTransactionState()
	 * @used-by Df_IPay_Action_GetPaymentAmount::getExpectedRequestType()
	 * @var string
	 */
	protected static $TRANSACTION_STATE__SERVICE_INFO = 'ServiceInfo';
	/**
	 * @used-by checkTransactionState()
	 * @used-by Df_IPay_Action_ConfirmPaymentByShop::getExpectedRequestType()
	 * @var string
	 */
	protected static $TRANSACTION_STATE__START = 'TransactionStart';
	/**
	 * @used-by getRequestHeader_Signature()
	 * @used-by processBeforeRedirect()
	 * @var string
	 */
	private static $HEADER__SIGNATURE = 'ServiceProvider-Signature';
	/**
	 * @used-by getRequestParam_Signature()
	 * @used-by getResponseHeader_Signature()
	 * @var string
	 */
	private static $SIGNATYPE_TYPE = 'SALT+MD5';
}