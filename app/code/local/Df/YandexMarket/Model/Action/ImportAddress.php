<?php
class Df_YandexMarket_Model_Action_ImportAddress extends Df_Core_Model_Action {
	/**
	 * @override
	 * @return string
	 */
	protected function getRedirectLocation() {return RM_URL_CHECKOUT;}

	/**
	 * @override
	 * @see Df_Core_Model_Action::_process()
	 * @used-by Df_Core_Model_Action::process()
	 * @return void
	 */
	protected function _process() {
		if ($this->getAddressType()) {
			Df_YandexMarket_AddressSession::set($this->getAddressType(), $this->getAddressData());
		}
	}

	/**
	 * @param string $key
	 * @return string|null
	 */
	private function _post($key) {return $this->getRequest()->getPost($key);}

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
		if (!isset($this->{__METHOD__})) {
			/** @var string $result */
			$result = '';
			if (df_bool($this->getRequest()->getCookie('rm_yandex_market_address_billing'))) {
				$result = Df_Checkout_Block_Frontend_Ergonomic_Address::TYPE__BILLING;
			}
			else if (df_bool($this->getRequest()->getCookie('rm_yandex_market_address_shipping'))) {
				$result = Df_Checkout_Block_Frontend_Ergonomic_Address::TYPE__SHIPPING;
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}
	
	/** @return string|null */
	private function getCountryId() {return rm_country_ntc_ru($this->_post('country'));}

	/** @return Mage_Checkout_Model_Session */
	private function getSession() {return df_session_checkout();}

	/** @return string */
	private function getStreet() {
		return df_ccc(', '
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
}