<?php
abstract class Df_Shipping_Model_Carrier
	extends Mage_Shipping_Model_Carrier_Abstract
	implements Mage_Shipping_Model_Carrier_Interface, Df_Core_Model_PaymentShipping_Method {
	/**
	 * Обратите внимание, что при браковке запроса в методе proccessAdditionalValidation
	 * модуль может показать на экране оформления заказа диагностическое сообщение,
	 * вернув из этого метода объект класса Mage_Shipping_Model_Rate_Result_Error.
	 *
	 * При браковке запроса в методе collectRates модуль такой возможности лишён.
	 * @override
	 * @param Mage_Shipping_Model_Rate_Request $request
	 * @return Mage_Shipping_Model_Rate_Result|bool|null
	 */
	public function collectRates(Mage_Shipping_Model_Rate_Request $request) {
		/** @var Df_Shipping_Model_Collector $collector */
		$collector = df_model($this->getCollectorClass(), array(
			Df_Shipping_Model_Collector::P__CARRIER => $this
			,Df_Shipping_Model_Collector::P__RATE_REQUEST => $this->createRateRequest($request)
		));
		df_assert($collector instanceof Df_Shipping_Model_Collector);
		return $collector->getRateResult();
	}

	/**
	 * @param Mage_Shipping_Model_Rate_Request $request
	 * @return Df_Shipping_Model_Rate_Request
	 */
	public function createRateRequest(Mage_Shipping_Model_Rate_Request $request) {
		return
			Df_Shipping_Model_Rate_Request::i(
				array_merge(
					$request->getData()
					,array(Df_Shipping_Model_Rate_Request::P__CARRIER => $this)
				)
			)
		;
	}

	/**
	 * @param string $message
	 * @param array(string => string) $variables [optional]
	 * @return string
	 */
	public function evaluateMessage($message, array $variables = array()) {
		return strtr($message, array_merge($this->getMessageVariables(), $variables));
	}

	/**
	 * Используется в 3-x местах:
	 * 1) В административной части для формирования перечня способов доставки
	 * 	  для применения к ним ценовых правил.
	 * 2) в Google Checkout
	 * 3) @see Df_Shipping_Model_Carrier::hasTheOnlyMethod()
	 * При этом систему интересуют только коды и названия способов оплаты,
	 * предоставляемые данным модулем.
	 * @override
	 * @return array(string => string)
	 */
	public function getAllowedMethods() {
		if (!isset($this->{__METHOD__})) {
			/** @var array(string => string) $result */
			$result = array();
			foreach ($this->getAllowedMethodsAsArray() as $methodId => $methodData) {
				/** @var string $methodId */
				/** @var array(string => mixed) $methodData */
				df_assert_string($methodId);
				df_assert_array($methodData);
				/** @var string $title */
				$title = df_a($methodData, 'title', $methodId);
				df_assert_string($title);
				$result[$methodId] = $title;
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return array(string => array(string => string)) */
	public function getAllowedMethodsAsArray() {
		if (!isset($this->{__METHOD__})) {
			/** @var Mage_Core_Model_Config_Element|null $configNode */
			$configNode =
				df()->config()->getNodeByKey(
					rm_config_key('df', 'shipping', $this->getRmId(), 'allowed-methods')
				)
			;
			$this->{__METHOD__} = is_null($configNode) ? array() : $configNode->asCanonicalArray();
			/**
			 * @see Varien_Simplexml_Element::asCanonicalArray может возвращать строку в случае,
			 * когда структура исходных данных не соответствует массиву.
			 */
			df_result_array($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/**
	 * Возвращает глобальный идентификатор способа доставки
	 * (добавляет к идентификатору способа доставки внутри Российской сборки
	 * приставку «df-»)
	 * @override
	 * @return string
	 */
	public function getCarrierCode() {
		// Переменная _code объявлена в родительском классе.
		if (!isset($this->_code)) {
			$this->_code = self::getCodeByRmId($this->getRmId());
			df_result_string($this->_code);
		}
		return $this->_code;
	}

	/**
	 * Получаем заданное ранее администратором
	 * значение конкретной настройки способа доставки.
	 * Обратите внимание, что Mage_Shipping_Model_Carrier_Abstract::getConfigData,
	 * в отличие от Mage_Payment_Model_Method_Abstract::getConfigData
	 * не получает магазин в качестве второго параметра
	 * @override
	 * @param string $field
	 * @return mixed
	 */
	public function getConfigData($field) {
		df_param_string($field, 0);
		return $this->getRmConfig()->getVar($field);
	}

	/**
	 * Получаем заданное ранее администратором
	 * значение конкретной настройки способа доставки
	 * @override
	 * @param string $field
	 * @return bool
	 */
	public function getConfigFlag($field) {
		df_param_string($field, 0);
		return rm_bool($this->getConfigData($field));
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
	 * Возвращает идентификатор способа доставки внутри Российской сборки
	 * (без приставки «df-»)
	 * Этот метод публичен, потому что использутся классами:
	 * @see Df_Shipping_Model_ConfigManager_Const
	 * @see Df_Shipping_Model_ConfigManager_Var
	 * @return string
	 */
	public function getRmId() {
		return Df_Core_Model_ClassManager::s()->getFeatureCode($this);
	}

	/** @return string */
	protected function getCollectorClass() {
		return
			Df_Core_Model_ClassManager::s()->getResourceClass(
				$caller = $this
				,$resourceSuffix = 'Model_Collector'
				,$defaultResult = Df_Shipping_Model_Collector::_CLASS
			)
		;
	}

	/** @return Mage_Shipping_Model_Rate_Result */
	protected function getRateResult() {
		if (!isset($this->{__METHOD__})) {
			/** @var Mage_Shipping_Model_Rate_Result $result */
			$this->{__METHOD__} = df_model('shipping/rate_result');
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param int|string|null|Mage_Core_Model_Store $storeId[optional]
	 * @return Df_Shipping_Model_Config_Facade
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
			/** @var Mage_Core_Model_Store $store */
			$store = Mage::app()->getStore($storeId);
			df_assert($store instanceof Mage_Core_Model_Store);
			/** @var Df_Shipping_Model_Config_Facade $result */
			$result =
				df_model($this->getRmConfigClass(), array(
					Df_Shipping_Model_Config_Facade::P__CONST_MANAGER =>
						Df_Shipping_Model_ConfigManager_Const::i($this, $store)
					,Df_Shipping_Model_Config_Facade::P__VAR_MANAGER =>
						Df_Shipping_Model_ConfigManager_Var::i($this, $store)
					,Df_Shipping_Model_Config_Facade::P__CONFIG_CLASS__ADMIN =>
						$this->getConfigClassAdmin()
					,Df_Shipping_Model_Config_Facade::P__CONFIG_CLASS__FRONTEND =>
						$this->getConfigClassFrontend()
					,Df_Shipping_Model_Config_Facade::P__CONFIG_CLASS__SERVICE =>
						$this->getConfigClassService()
				))
			;
			df_assert($result instanceof Df_Shipping_Model_Config_Facade);
			$this->{__METHOD__}[$storeId] = $result;
		}
		return $this->{__METHOD__}[$storeId];
	}

	/** @return Mage_Core_Model_Store */
	public function getRmStore() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Mage::app()->getStore($this->getDataUsingMethod(self::P__STORE));
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return string
	 */
	public function getTitle() {return $this->getRmConfig()->frontend()->getTitle();}

	/** @return bool */
	public function hasTheOnlyMethod() {return 1 === count($this->getAllowedMethods());}

	/**
	 * @override
	 * @return bool
	 */
	public function isActive() {
		return parent::isActive() && df_enabled($this->getRmFeatureCode(), $this->getRmStore());
	}

	/**
	 * Работает ли модуль в тестовом режиме?
	 * Обратите внимание, что если в настройках отсутствует ключ «test»,
	 * то модуль будет всегда находиться в рабочем режиме.
	 * @return bool
	 */
	public function isTestMode() {return $this->getRmConfig()->service()->isTestMode();}

	/**
	 * Обратите внимание, что при браковке запроса в методе proccessAdditionalValidation
	 * модуль может показать на экране оформления заказа диагностическое сообщение,
	 * вернув из этого метода объект класса Mage_Shipping_Model_Rate_Result_Error.
	 * При браковке запроса в методе collectRates модуль такой возможности лишён.
	 * @override
	 * @param Mage_Shipping_Model_Rate_Request $request
  	 * @return Df_Shipping_Model_Carrier|Mage_Shipping_Model_Rate_Result_Error|boolean
	 */
	public function proccessAdditionalValidation(Mage_Shipping_Model_Rate_Request $request) {
		/** @var Df_Shipping_Model_Carrier|Mage_Shipping_Model_Rate_Result_Error|boolean $result */
		$result = parent::proccessAdditionalValidation($request);
		if (
				(false !== $result)
			&&
				!($result instanceof Mage_Shipping_Model_Rate_Result_Error)
		) {
			try {
				/** @var Df_Shipping_Model_Rate_Request $rmRequest */
				$rmRequest = $this->createRateRequest($request);
				$rmRequest->getOriginCity();
				if (
						$this->getRmConfig()->frontend()->needDisableForShopCity()
					&&
						$rmRequest->isOriginTheSameAsDestination()
				) {
					/** @var Df_Shipping_Model_Rate_Result_Error $result */
					$result =
						Df_Shipping_Model_Rate_Result_Error::i(
							$this
							,$rmRequest->evaluateMessage(
								'Склад нашего магазина расположен тоже {в месте доставки}.'
								."\r\nВыберите доставку курьером."
							)
						)
					;
				}
			}
			catch(Exception $e) {
				/** @var string $message */
				$message = rm_ets($e);
				if ($e instanceof Df_Core_Exception_Internal) {
					df_notify_exception($e);
					$message = df_mage()->shippingHelper()->__(self::T_INTERNAL_ERROR);
				}
				$result = Df_Shipping_Model_Rate_Result_Error::i($this, $message);
			}
		}
		return $result;
	}

	/** @return array(string => string) */
	protected function getMessageVariables() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = array(
				'{carrier}' => $this->getTitle()
				,'{название службы доставки в именительном падеже}' => $this->getTitle()
				,'{phone}' => df_cfg()->base()->getStorePhone($this->getRmStore())
				,'{телефон магазина}' => df_cfg()->base()->getStorePhone($this->getRmStore())
				,'{название службы доставки в творительном падеже}' =>
					strtr(
						'службой «<b>{carrier}</b>»'
						,array('{carrier}' => $this->getTitle())
					)
				,'{название службы и способа доставки в творительном падеже}' =>
					strtr(
						'службой «<b>{carrier}</b>»'
						,array('{carrier}' => $this->getTitle())
					)
			);
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	protected function getRmConfigClass() {
		return
			Df_Core_Model_ClassManager::s()->getResourceClass(
				$caller = $this
				,$resourceSuffix = 'Model_Config_Facade'
				,$defaultResult = Df_Shipping_Model_Config_Facade::_CLASS
			)
		;
	}

	/** @return string */
	protected function getRmFeatureCode() {
		return Df_Core_Model_ClassManager::s()->getFeatureCode($this);
	}

	/** @return string */
	private function getConfigClassAdmin() {
		return
			Df_Core_Model_ClassManager::s()->getResourceClass(
				$caller = $this
				,$resourceSuffix = 'Model_Config_Area_Admin'
				,$defaultResult = Df_Shipping_Model_Config_Area_Admin::_CLASS
			)
		;
	}

	/** @return string */
	private function getConfigClassFrontend() {
		return
			Df_Core_Model_ClassManager::s()->getResourceClass(
				$caller = $this
				,$resourceSuffix = 'Model_Config_Area_Frontend'
				,$defaultResult = Df_Shipping_Model_Config_Area_Frontend::_CLASS
			)
		;
	}

	/** @return string */
	private function getConfigClassService() {
		return
			Df_Core_Model_ClassManager::s()->getResourceClass(
				$caller = $this
				,$resourceSuffix = 'Model_Config_Area_Service'
				,$defaultResult = Df_Shipping_Model_Config_Area_Service::_CLASS
			)
		;
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->getCarrierCode();
	}
	const _CLASS = __CLASS__;
	const P__STORE = 'store';
	const RM__ID_PREFIX = 'df';
	const RM__ID_SEPARATOR = '-';
	const T_INTERNAL_ERROR =
		'This shipping method is currently unavailable. If you would like to ship using this shipping method, please contact us.'
	;
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