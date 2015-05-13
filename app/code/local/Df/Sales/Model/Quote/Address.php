<?php
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
			$result = Mage::getStoreConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_COUNTRY);
		}
		if (!is_null($result)) {
			df_result_string($result);
		}
		return $result;
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
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
					df_cfg()->checkout()->_interface()->needShowAllStepsAtOnce()
				&&
					$this->getAddressType()
			;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Customer_Model_Address_Validator */
	private function getValidator() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Customer_Model_Address_Validator::i($this);
		}
		return $this->{__METHOD__};
	}

	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Sales_Model_Quote_Address
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}