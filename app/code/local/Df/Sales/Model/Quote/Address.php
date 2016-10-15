<?php
/**
 * @method Df_Sales_Model_Quote_Address setStreet(string $value)
 */
class Df_Sales_Model_Quote_Address extends Mage_Sales_Model_Quote_Address {
	/**
	 * Цель перекрытия —
	 * при отсутствии информации о стране считать страной Россию вместо null.
	 * @override
	 * @return string|null
	 */
	public function getCountryId() {
		/** @var string|null $result */
		$result = parent::_getData('country_id');
		if (!$result) {
			/**
			 * Нельзя использовать df_mage()->coreHelper()->getDefaultCountry(),
			 * потому что метод Mage_Core_Helper_Data::getDefaultCountry
			 * отсутствует в Magento 1.4.0.1
			 */
			$result = Df_Core_Helper_DataM::s()->getDefaultCountry();
		}
		if (!is_null($result)) {
			df_result_string($result);
		}
		return $result;
	}

	/** @return void */
	public function convertStreetToText() {
		if (is_array($this->getStreet())) {
			$this->setStreet($this->getStreetAsText());
		}
	}

	/**
	 * Намеренно не кэшируем результат:
	 * на производительность системы в целом кэширование адреса никак не повлияет,
	 * а вот вероятность ошибки при изменении адреса увеличивает.
	 * @return string
	 */
	public function getStreetAsText() {
		/**
		 * Обратите внимание, что @uses getStreetFull()
		 * может вернуть как строку, так и массив строк.
		 */
		return
			!is_array($this->getStreetFull())
			? $this->getStreetFull()
			: df_cc_n(df_clean($this->getStreetFull()))
		;
	}

	/**
	 * Цель перекрытия —
	 * учёт настроек видимости и обязательности для заполнения полей оформления заказа
	 * модуля «Удобная настройка витрины».
	 * @override
	 * @return bool|string[]
	 * Метод возвращает либо true, либо перечень диагностических сообщений валидатора.
	 */
	public function validate() {
		/** @var bool|string[] $result */
		$result =
			!$this->isCustomValidationNeeded()
			? parent::validate()
			: $this->getValidator()->validate()
		;
		if (!is_array($result)) {
			df_result_boolean($result);
		}
		return $result;
	}

	/** @return bool */
	private function isCustomValidationNeeded() {
		return df_checkout_ergonomic() && $this->getAddressType();
	}

	/** @return Df_Customer_Model_Address_Validator */
	private function getValidator() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Customer_Model_Address_Validator::i($this);
		}
		return $this->{__METHOD__};
	}


	const P__STREET = 'street';
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Sales_Model_Quote_Address
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}