<?php
class Df_YandexMarket_Model_Action_ImportAddress extends Df_Core_Model_Controller_Action {
	/**
	 * @override
	 * @return string
	 */
	protected function generateResponseBody() {
		/** @var mixed[] $addresses */
		$addresses = $this->getSession()->getData(self::SESSION__ADDRESSES_FROM_YANDEX_MARKET);
		if (!is_array($addresses)) {
			$addresses = array();
		}
		$addresses[$this->getAddressType()]= $this->getAddressData();
		$this->getSession()->setData(self::SESSION__ADDRESSES_FROM_YANDEX_MARKET, $addresses);
		$this->redirect(Df_Checkout_Const::URL__CHECKOUT);
		return '';
	}

	/**
	 * @param string $paramName
	 * @param string $paramLabel
	 * @return string|null
	 */
	private function composeStreetPart($paramName, $paramLabel) {
		/** @var string|null $paramValue */
		$paramValue = $this->_post($paramName);
		return !$paramValue ? null : rm_concat_clean(' ', $paramLabel, $paramValue);
	}

	/** @return array(string => string) */
	private function getAddressData() {
		return df_clean(array(
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
		));
	}
	
	/** @return string */
	private function getAddressType() {
		/** @var string $result */
		$result = '';
		if (rm_bool($this->getRequest()->getCookie('rm_yandex_market_address_billing'))) {
			$result = Df_Checkout_Block_Frontend_Ergonomic_Address::TYPE__BILLING;
		}
		else if (rm_bool($this->getRequest()->getCookie('rm_yandex_market_address_shipping'))) {
			$result = Df_Checkout_Block_Frontend_Ergonomic_Address::TYPE__SHIPPING;
		}
		return $result;
	}
	
	/** @return string|null */
	private function getCountryId() {
		return df_h()->directory()->country()->getIso2CodeByName($this->_post('country'));
	}

	/** @return Mage_Checkout_Model_Session */
	private function getSession() {return rm_session_checkout();}

	/** @return string */
	private function getStreet() {
		return rm_concat_clean(', '
			,$this->composeStreetPart('street', '')
			,$this->composeStreetPart('building', 'дом')
			,$this->composeStreetPart('suite', 'корпус')
			,$this->composeStreetPart('flat', 'квартира')
			,$this->composeStreetPart('entrance', 'подъезд')
			,$this->composeStreetPart('floor', 'этаж')
			,$this->composeStreetPart('intercom', 'домофон')
			,!$this->_post('cargolift') ? null	: 'есть грузовой лифт'
			,$this->composeStreetPart('comment', '')
		);
	}

	/**
	 * @param string $key
	 * @return string|null
	 */
	private function _post($key) {return $this->getRequest()->getPost($key);}

	const _CLASS = __CLASS__;
	const SESSION__ADDRESSES_FROM_YANDEX_MARKET = 'rm_addresses_from_yandex_market';
	/**
	 * @static
	 * @param Df_YandexMarket_AddressController $controller
	 * @return Df_YandexMarket_Model_Action_ImportAddress
	 */
	public static function i(Df_YandexMarket_AddressController $controller) {
		return new self(array(self::P__CONTROLLER => $controller));
	}
}