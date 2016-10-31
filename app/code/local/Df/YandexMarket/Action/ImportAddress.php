<?php
namespace Df\YandexMarket\Action;
use Df_Checkout_Block_Frontend_Ergonomic_Address as A;
class ImportAddress extends \Df_Core_Model_Action {
	/**
	 * @override
	 * @return string
	 */
	protected function redirectLocation() {return RM_URL_CHECKOUT;}

	/**
	 * @override
	 * @see Df_Core_Model_Action::_process()
	 * @used-by Df_Core_Model_Action::process()
	 * @return void
	 */
	protected function _process() {
		if ($this->getAddressType()) {
			\Df\YandexMarket\AddressSession::set($this->getAddressType(), $this->getAddressData());
		}
	}

	/**
	 * @param string $key
	 * @return string|null
	 */
	private function _post($key) {return $this->request()->getPost($key);}

	/**
	 * @param string $paramName
	 * @param string $paramLabel
	 * @return string|null
	 */
	private function composeStreetPart($paramName, $paramLabel) {
		/** @var string|null $paramValue */
		$paramValue = $this->_post($paramName);
		return !$paramValue ? null : df_ccc(' ', $paramLabel, $paramValue);
	}

	/**
	 * @param string $name
	 * @return string|null
	 */
	private function cookie($name) {return
		$this->request()->getCookie("rm_yandex_market_address_{$name}")
	;}

	/** @return array(string => string) */
	private function getAddressData() {return df_clean([
		'company' => ''
		,'firstname' => $this->_post('firstname')
		,'lastname' => $this->_post('lastname')
		,'middlename' => $this->_post('fathersname')
		,'telephone' => $this->_post('phone')
		,'country' => $this->_post('country')
		,'country_id' => $this->getCountryId()
		,'region' => ''
		,'city' => $this->_post('city')
		,'postcode' => $this->_post('zip')
		,'street' => $this->getStreet()
		,'email' => $this->_post('email')
		,'fax' => ''
	]);}

	/** @return string */
	private function getAddressType() {return dfc($this, function() {return
		$this->cookie('billing') ? A::TYPE__BILLING : (
			$this->cookie('shipping') ? A::TYPE__SHIPPING : ''
		)
	;});}
	
	/** @return string|null */
	private function getCountryId() {return df_country_ntc_ru($this->_post('country'));}

	/** @return \Mage_Checkout_Model_Session */
	private function getSession() {return df_session_checkout();}

	/** @return string */
	private function getStreet() {return df_ccc(', '
		,$this->composeStreetPart('street', '')
		,$this->composeStreetPart('building', 'дом')
		,$this->composeStreetPart('suite', 'корпус')
		,$this->composeStreetPart('flat', 'квартира')
		,$this->composeStreetPart('entrance', 'подъезд')
		,$this->composeStreetPart('floor', 'этаж')
		,$this->composeStreetPart('intercom', 'домофон')
		,!$this->_post('cargolift') ? null	: 'есть грузовой лифт'
		,$this->composeStreetPart('comment', '')
	);}
}