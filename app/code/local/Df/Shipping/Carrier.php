<?php
/**
 * @method int|null getStore()
 * @see Mage_Shipping_Model_Shipping::getCarrierByCode():
	if ($storeId) {
		$obj->setStore($storeId);
	}
 * @method setStore(int $value)
 */
abstract class Df_Shipping_Carrier
	extends Mage_Shipping_Model_Carrier_Abstract
	implements Mage_Shipping_Model_Carrier_Interface, Df_Checkout_Module_Main {
	/**
	 * Обратите внимание, что при браковке запроса в методе @see proccessAdditionalValidation()
	 * модуль может показать на экране оформления заказа диагностическое сообщение,
	 * вернув из этого метода объект класса @see Mage_Shipping_Model_Rate_Result_Error.
	 * При браковке запроса в методе @see collectRates() модуль такой возможности лишён.
	 * @override
	 * @used-by Mage_Shipping_Model_Shipping::collectCarrierRates()
	 * Родительский метод (абстрактный): Mage_Shipping_Model_Carrier_Abstract::collectRates()
	 * @param Mage_Shipping_Model_Rate_Request $request
	 * @return Mage_Shipping_Model_Rate_Result|bool|null
	 */
	public function collectRates(Mage_Shipping_Model_Rate_Request $request) {
		return $this->_lastRateResult = Df_Shipping_Collector::r($this, $request);
	}

	/**
	 * @override
	 * @see Df_Checkout_Module_Main::config()
	 * @return Df_Checkout_Module_Config_Facade
	 */
	public function config() {return Df_Checkout_Module_Config_Facade::s($this);}

	/**
	 * @used-by Df_Shipping_Rate_Request::getDeclaredValue()
	 * @return Df_Shipping_Config_Area_Admin
	 */
	public function configA() {return $this->config()->admin();}

	/**
	 * @used-by Df_Shipping_Rate_Request::evaluateMessage()
	 * @param string $message
	 * @param array(string => string) $variables [optional]
	 * @return string
	 */
	public function evaluateMessage($message, array $variables = array()) {
		return strtr($message, $variables + $this->getMessageVariables());
	}

	/**
	 * @see Mage_Shipping_Model_Carrier_Interface::getAllowedMethods()
	 * Используется в административной части для формирования перечня способов доставки,
	 * к которым администратор может применить ценовые правила.
	 * @used-by Mage_Adminhtml_Model_System_Config_Source_Shipping_Allmethods::toOptionArray()
	 * При этом систему интересуют только коды и названия способов доставки,
	 * предоставляемые данным модулем.
	 * Обратите внимание, что если мы не хотим предоставлять привязку ценовых правил
	 * к способам доставки данного перевозчика, то мы можем просто вернуть пустой массив.
	 * для применения к ним ценовых правил:
	 * @override
	 * @return array(string => string)
	 */
	public function getAllowedMethods() {return array();}

	/**
	 * Получаем заданное ранее администратором
	 * значение конкретной настройки способа доставки.
	 * Обратите внимание, что @see Mage_Shipping_Model_Carrier_Abstract::getConfigData(),
	 * в отличие от @see Mage_Payment_Model_Method_Abstract::getConfigData()
	 * не получает магазин в качестве второго параметра
	 *
	 * 2015-03-27
	 * Важный момент.
	 * Значение опции «Показывать ли способ доставки на витрине в том случае,
	 * когда он по каким-либо причинам неприменим к текущему заказу?»
	 * («frontend__display_diagnostic_messages», «showmethod»)
	 * мы учитываем только в том случае, если
	 * модуль доставки корректно выполнил свою работу
	 * и способ доставки оказался неприменим к текущему заказу покупателя.
	 *
	 * Если же работа модуля доставки завершилась сбойно,
	 * то неизвестно, применим ли способ доставки к текущему заказу покупателя,
	 * и будет неправильно просто так взять и скрыть способ доставки.
	 * Вместо этого мы показываем покупателю сообщение типа
	 * «Для доставки Вашего заказа {службой},
	 * пожалуйста, позвоните нам по телефону {телефон магазина}.»
	 * («df_shipping/message/failure__general»).
	 *
	 * @override
	 * @used-by Mage_Shipping_Model_Shipping::collectCarrierRates()
	 * @used-by getConfigFlag()
	 * @param string $field
	 * @return mixed
	 */
	public function getConfigData($field) {
		return
			'showmethod' === $field
			&& $this->_lastRateResult
			&& $this->_lastRateResult->isInternalError()
				? true
				: $this->config()->getVar($field)
		;
	}

	/**
	 * Получаем заданное ранее администратором
	 * значение конкретной настройки способа доставки
	 * @override
	 * @param string $field
	 * @return bool
	 */
	public function getConfigFlag($field) {return rm_bool($this->getConfigData($field));}

	/**
	 * @override
	 * @see Df_Checkout_Module_Main::getRmId()
	 * @used-by isActive()
	 * @used-by Df_Checkout_Module_Config_Manager::adaptKey()
	 * @return string
	 */
	final public function getRmId() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_module_id($this, '-');
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @see Df_Checkout_Module_Main::getCheckoutModuleType()
	 * @used-by Df_Checkout_Module_Bridge::convention()
	 * @used-by Df_Checkout_Module_Config_Manager::s()
	 * @used-by Df_Checkout_Module_Config_Area_No::s()
	 * @return string
	 */
	public function getCheckoutModuleType() {return Df_Checkout_Module_Bridge::_type(__CLASS__);}

	/**
	 * @override
	 * @see Df_Checkout_Module_Main::getConfigTemplates()
	 * @used-by Df_Checkout_Module_Config_Manager::getTemplates()
	 * @return array(string => string)
	 */
	public function getConfigTemplates() {return array();}

	/**
	 * @see Df_Checkout_Module_Main::getTitle()
	 * @override
	 * @override
	 * @return string
	 */
	public function getTitle() {return $this->configF()->getTitle();}

	/**
	 * @param Exception|string $message
	 * @return Df_Shipping_Rate_Result_Error
	 */
	protected function createRateResultError($message) {
		return Df_Shipping_Rate_Result_Error::i($this, $message);
	}

	/** @return array(string => string) */
	protected function getMessageVariables() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = array(
				'{carrier}' => $this->getTitle()
				,'{название службы доставки в именительном падеже}' => $this->getTitle()
				,'{phone}' => df_cfg()->base()->getStorePhone($this->getStore())
				,'{телефон магазина}' => df_cfg()->base()->getStorePhone($this->getStore())
				,'{название службы доставки в творительном падеже}' =>
					$this->названиеВТворительномПадеже()
				,'{название службы и способа доставки в творительном падеже}' =>
					$this->названиеВТворительномПадеже()

			);
		}
		return $this->{__METHOD__};
	}

	/**
	 * @used-by getTitle()
	 * @return Df_Shipping_Config_Area_Frontend
	 */
	private function configF() {return $this->config()->frontend();}

	/**
	 * @used-by getMessageVariables()
	 * @return string
	 */
	private function названиеВТворительномПадеже() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $result */
			$this->{__METHOD__} =
				$this->titleMorhper()
					? "<b>{$this->titleMorhper()->getInCaseInstrumental()}</b>"
					: "службой «<b>{$this->getTitle()}</b>»"
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @used-by названиеВТворительномПадеже()
	 * @return Df_Localization_Morpher_Response|null
	 */
	private function titleMorhper() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_n_set(
				!rm_has_russian_letters($this->getTitle())
				? null
				: Df_Localization_Morpher::s()->getResponseSilent($this->getTitle())
			);
		}
		return rm_n_get($this->{__METHOD__});
	}

	/**
	 * Обратите внимание, что родительский класс нигде не инициализирует
	 * переменную @uses Mage_Shipping_Model_Carrier_Abstract::_code
	 * однако использует в неперекрываемом нами методе
	 * @see Mage_Shipping_Model_Carrier_Abstract::checkAvailableShipCountries()
		$error = Mage::getModel('shipping/rate_result_error');
		$error->setCarrier($this->_code);
	 * Родительский класс также использует её в перекрываемых нами методах
	 * @see Mage_Shipping_Model_Carrier_Abstract::getConfigData()
	 * и @see Mage_Shipping_Model_Carrier_Abstract::getConfigFlag()
	 * @see Mage_Shipping_Model_Carrier_Abstract::getCarrierCode()
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_code = 'df-' . $this->getRmId();
	}

	/**
	 * @used-by collectRates()
	 * @used-by getConfigData()
	 * @var Df_Shipping_Rate_Result|null
	 */
	private $_lastRateResult = null;

	/**
	 * @used-by Df_Checkout_Module_Config_Facade::_construct()
	 * @used-by Df_PonyExpress_Model_Request_Rate::_construct()
	 * @used-by Df_Shipping_Model_Bridge::_construct()
	 * @used-by Df_Shipping_Config_Area::_construct()
	 * @used-by Df_Shipping_Config_Manager::_construct()
	 */
	const _C = __CLASS__;
}