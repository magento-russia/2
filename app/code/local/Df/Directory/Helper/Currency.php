<?php
class Df_Directory_Helper_Currency extends Mage_Core_Helper_Abstract {
	/**
	 * @param float $amountInBaseCurrency
	 * @param string|Mage_Directory_Model_Currency $customCurrency
	 * @param Mage_Core_Model_Store|int|string|null $store [optional]
	 * @return float
	 */
	public function convertFromBase($amountInBaseCurrency, $customCurrency, $store = null) {
		df_param_float($amountInBaseCurrency, 0);
		$store = Mage::app()->getStore($store);
		if (!is_string($customCurrency)) {
			df_assert($customCurrency instanceof Mage_Directory_Model_Currency);
			/**
			 * Метод ядра использует только код валюты
			 */
			$customCurrency = $customCurrency->getCode();
		}
		/**
		 * Кэшировать курсы не надо,
		 * потому что это делает системный метод Mage_Directory_Model_Currency::getRate
		 */
		/** @var float $result */
		$result = $store->getBaseCurrency()->convert($amountInBaseCurrency, $customCurrency);
		df_result_float($result);
		return $result;
	}

	/**
	 * @param float $amountInBaseCurrency
	 * @param Mage_Core_Model_Store|int|string|null $store [optional]
	 * @return float
	 */
	public function convertFromBaseToHryvnias($amountInBaseCurrency, $store = null) {
		df_param_float($amountInBaseCurrency, 0);
		$store = Mage::app()->getStore($store);
		/** @var float $result */
		$result = $store->getBaseCurrency()->convert($amountInBaseCurrency, $this->getHryvnia());
		df_result_float($result);
		return $result;
	}

	/**
	 * @param float $amountInBaseCurrency
	 * @param Mage_Core_Model_Store|int|string|null $store [optional]
	 * @return float
	 */
	public function convertFromBaseToRoubles($amountInBaseCurrency, $store = null) {
		df_param_float($amountInBaseCurrency, 0);
		$store = Mage::app()->getStore($store);
		/** @var float $result */
		$result = $store->getBaseCurrency()->convert($amountInBaseCurrency, $this->getRouble());
		df_result_float($result);
		return $result;
	}

	/**
	 * @param float $amountInBaseCurrency
	 * @param Mage_Core_Model_Store|int|string|null $store [optional]
	 * @return float
	 */
	public function convertFromBaseToTenge($amountInBaseCurrency, $store = null) {
		df_param_float($amountInBaseCurrency, 0);
		$store = Mage::app()->getStore($store);
		/** @var float $result */
		$result = $store->getBaseCurrency()->convert($amountInBaseCurrency, $this->getTenge());
		df_result_float($result);
		return $result;
	}

	/**
	 * @param float $amountInHryvnias
	 * @param Mage_Core_Model_Store|int|string|null $store [optional]
	 * @return float
	 */
	public function convertFromHryvniasToBase($amountInHryvnias, $store = null) {
		df_param_float($amountInHryvnias, 0);
		$store = Mage::app()->getStore($store);
		/** @var float $result */
		$result = $this->convertToBase($amountInHryvnias, $this->getHryvnia(), $store);
		df_result_float($result);
		return $result;
	}

	/**
	 * @param float $amountInRoubles
	 * @param Mage_Core_Model_Store|int|string|null $store [optional]
	 * @return float
	 */
	public function convertFromRoublesToBase($amountInRoubles, $store = null) {
		df_param_float($amountInRoubles, 0);
		$store = Mage::app()->getStore($store);
		/** @var float $result */
		$result = $this->convertToBase($amountInRoubles, $this->getRouble(), $store);
		df_result_float($result);
		return $result;
	}

	/**
	 * @param float $amountInTenge
	 * @param Mage_Core_Model_Store|int|string|null $store [optional]
	 * @return float
	 */
	public function convertFromTengeToBase($amountInTenge, $store = null) {
		df_param_float($amountInTenge, 0);
		$store = Mage::app()->getStore($store);
		/** @var float $result */
		$result = $this->convertToBase($amountInTenge, $this->getTenge(), $store);
		df_result_float($result);
		return $result;
	}

	/**
	 * @param float $amountInCustomCurrency
	 * @param string|Mage_Directory_Model_Currency $customCurrency
	 * @param Mage_Core_Model_Store|int|string|null $store [optional]
	 * @return float
	 */
	public function convertToBase($amountInCustomCurrency, $customCurrency, $store = null) {
		df_param_float($amountInCustomCurrency, 0);
		$store = Mage::app()->getStore($store);
		if (is_string($customCurrency)) {
			$customCurrency = Df_Directory_Model_Currency::ld($customCurrency);
		}
		df_assert($customCurrency instanceof Mage_Directory_Model_Currency);
		/** @var float $result */
		$result =
			/**
			 * Обратите внимание, что перевод из одной валюты в другую
			 * надо осуществлять только в направлении 'базовая валюта' => 'второстепенная валюта',
			 * но не наоборот
			 * (Magento не умеет выполнять первод 'второстепенная валюта' => 'базовая валюта'
			 * даже при наличии курса 'базовая валюта' => 'второстепенная валюта',
			 * и возбуждает исключительную ситуацию).
			 */
				$amountInCustomCurrency
			*
				(1 / $store->getBaseCurrency()->convert(doubleval(1), $customCurrency))
		;
		return $result;
	}

	/** @return Df_Directory_Model_Currency */
	public function getBase() {
		return Df_Directory_Model_Currency::ld(
			Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE)
		);
	}

	/** @return Df_Directory_Model_Currency */
	public function getDollar() {
		return Df_Directory_Model_Currency::ld(Df_Directory_Model_Currency::USD);
	}

	/** @return Df_Directory_Model_Currency */
	public function getHryvnia() {
		return Df_Directory_Model_Currency::ld(Df_Directory_Model_Currency::UAH);
	}

	/** @return int */
	public function getPrecision() {
		// Странно, что результат этого метода раньше не кэшировался.
		// Обязательно надо кэшировать!
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				rm_loc()->needHideDecimals()
				? 0
				: df_a(df_mage()->core()->localeSingleton()->getJsPriceFormat(), 'requiredPrecision', 2)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Directory_Model_Currency */
	public function getRouble() {
		return Df_Directory_Model_Currency::ld(Df_Directory_Model_Currency::RUB);
	}

	/** @return Df_Directory_Model_Currency */
	public function getTenge() {
		return Df_Directory_Model_Currency::ld(Df_Directory_Model_Currency::KZT);
	}

	/** @return Df_Directory_Helper_Currency */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}