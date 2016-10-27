<?php
namespace Df\Payment\Request;
use \Df\Payment\Method\WithRedirect as MethodR;
abstract class Payment extends \Df\Payment\Request {
	/**
	 * @abstract
	 * @used-by params()
	 * @return array(string => string|int)
	 */
	abstract protected function _params();

	/**
	 * @used-by Df_IPay_Action_GetPaymentAmount::_process()
	 * @used-by \Df\Assist\Request\Payment::_params()
	 * @return string
	 */
	public function city() {return $this->address()->getCity();}

	/**
	 * @used-by Df_Uniteller_Request_Payment::_params()
	 * @return string
	 */
	protected function countryName() {return $this->address()->getCountryModel()->getName();}

	/**
	 * @used-by \Df\Assist\Request\Payment::_params()
	 * @return string
	 */
	protected function currencyCode() {return $this->configS()->getCurrencyCodeInServiceFormat();}

	/**
	 * @override
	 * @see \Df\Payment\Request::description()
	 * @return string
	 */
	protected function description() {return dfc($this, function() {return
		str_replace(
			array_keys($this->descriptionParams())
			,array_values($this->descriptionParams())
			,$this->configS()->description()
		)
	;});}

	/**
	 * @used-by \Df\Assist\Request\Payment::_params()
	 * @return string
	 */
	protected function email() {return $this->order()->getCustomerEmail();}

	/** @return string */
	public function getCustomerIpAddress() {return
		!df_controller() ? '' : df_controller()->getRequest()->getClientIp()
	;}

	/** @return string */
	protected function getCustomerNameFull() {return df_cc_s(
		$this->order()->getCustomerLastname()
		,$this->order()->getCustomerFirstname()
		,$this->order()->getCustomerMiddlename()
	);}

	/** @return string */
	protected function urlCustomerReturn() {return dfc($this, function() {return
		$this->method()->getCustomerReturnUrl($this->order())
	;});}

	/**
	 * @used-by description
	 * @return \Zend_Uri_Http
	 */
	protected function getStoreUri() {return dfc($this, function() {return
		\Zend_Uri_Http::fromString($this->store()->getBaseUrl(\Mage_Core_Model_Store::URL_TYPE_WEB))
	;});}

	/** @return array(string => string) */
	protected function descriptionParams() {return dfc($this, function() {return [
		'{order.id}' => $this->order()->getIncrementId()
		,'{shop.domain}' => $this->getStoreUri()->getHost()
		,'{shop.name}' => $this->store()->getName()
	];});}

	/**
	 * @used-by \Df\Assist\Request\Payment::_params()
	 * @return string
	 */
	protected function iso3() {return $this->address()->getCountryModel()->getIso3Code();}

	/**
	 * @used-by \Df\Assist\Request\Payment::_params()
	 * @used-by Df_OnPay_Request_Payment::_params()
	 * @used-by Df_RbkMoney_Request_Payment::_params()
	 * @used-by Df_WebPay_Request_Payment::_params()
	 * @return string
	 */
	protected function localeCode() {return $this->configS()->getLocaleCodeInServiceFormat();}

	/**
	 * @used-by \Df\Assist\Request\Payment::_params()
	 * @return string
	 */
	protected function nameFirst() {return $this->order()->getCustomerFirstname();}

	/**
	 * @used-by \Df\Assist\Request\Payment::_params()
	 * @return string
	 */
	protected function nameLast() {return $this->order()->getCustomerLastname();}

	/**
	 * @used-by \Df\Assist\Request\Payment::_params()
	 * @return string
	 */
	protected function nameMiddle() {return $this->order()->getCustomerMiddlename();}

	/**
	 * @override
	 * @see \Df\Payment\Request::order()
	 * @used-by \Df\Payment\Request::amount()
	 * @used-by \Df\Payment\Request::method()
	 * @used-by \Df\Payment\Request::payment()
	 * @return \Df_Sales_Model_Order
	 */
	protected function order() {return df_last_order();}

	/**
	 * @used-by \Df\Assist\Request\Payment::_params()
	 * @return string
	 */
	protected function phone() {return df_nts($this->address()->getTelephone());}

	/**
	 * @used-by \Df\Assist\Request\Payment::_params()
	 * @return string
	 */
	protected function postCode() {return df_nts($this->address()->getPostcode());}

	/**
	 * @param array(string => mixed) $params
	 * @return array(string => mixed)
	 */
	protected function preprocessParams(array $params) {return $this->chopParams($params);}

	/**
	 * @used-by \Df\Assist\Request\Payment::_params()
	 * @return string
	 */
	protected function regionCode() {return $this->region()->getCode();}

	/**
	 * @used-by \Df\Assist\Request\Payment::_params()
	 * @return string
	 */
	protected function street() {return implode(' ', df_clean($this->address()->getStreet()));}

	/**
	 * Этот метод используют только потомки:
	 * @used-by \Df\Interkassa\Request\Payment::_params()
	 * @used-by Df_Kkb_Request_Payment::_params()
	 * @used-by Df_LiqPay_Request_Payment::getParamsForXml()
	 * @used-by Df_WebMoney_Request_Payment::_params()
	 * @used-by Df_WebPay_Request_Payment::_params()
	 * @return string
	 */
	protected function urlConfirm() {return dfc($this, function() {return
		\Mage::getUrl($this->method()->getCode() . '/confirm')
	;});}

	/**
	 * @used-by city()
	 * @used-by iso3()
	 * @used-by phone()
	 * @used-by region()
	 * @used-by street()
	 * @return \Df_Sales_Model_Order_Address
	 */
	private function address() {return $this->order()->getBillingAddress();}

	/**
	 * @param string $text
	 * @param string $requestVarName
	 * @return string
	 */
	private function chopParam($text, $requestVarName) {
		df_param_string($text, 0);
		df_param_string($requestVarName, 1);
		/** @var int $maxLength */
		$maxLength = $this->method()->constManager()->requestVarMaxLength($requestVarName);
		/** @var string $result */
		$result =
			(0 >= $maxLength)
			? $text
			: mb_substr($text, 0, $maxLength, 'UTF-8')
		;
		return $result;
	}

	/**
	 * @param array(string => mixed) $params
	 * @return array(string => mixed)
	 */
	private function chopParams(array $params) {
		/** @var array(string => mixed) $result */
		$result = [];
		foreach ($params as $paramName => $paramValue) {
			/** @var string $paramName */
			/** @var mixed $paramValue */
			/**
			 * $paramName — всегда строка!
			 * $paramName нужно нам для того, чтобы заглянуть в config.xml
			 * и поискать там ограничение на длину $paramValue
			 */
			df_assert_string($paramName);
			$result[$paramName] =
				!is_string($paramValue)
				? $paramValue
				: $this->chopParam($paramValue, $paramName)
			;
		}
		return $result;
	}

	/**
	 * @used-by regionCode()
	 * @return \Mage_Directory_Model_Region
	 */
	private function region() {return $this->address()->getRegionModel();}

	/**
	 * @used-by \Df\Payment\Method\WithRedirect::getPaymentPageParams()
	 * @param MethodR $method
	 * @return array(string => string|int)
	 */
	public static function params(MethodR $method) {return dfcf(function($class) {
		/** @var $this $i */
		$i = df_ic(df_con($class, 'Request_Payment'), __CLASS__);
		$result = $i->preprocessParams($i->_params());
		if (df_my()) {
			/** @var string $module */
			$module = df_module_name($class);
			df_report("{$module}-{date}-{time}.log", df_json_encode_pretty($result));
		}
		return $result;
	}, [get_class($method)]);}
}