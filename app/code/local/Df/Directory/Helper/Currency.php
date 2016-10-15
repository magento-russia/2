<?php
class Df_Directory_Helper_Currency extends Mage_Core_Helper_Abstract {
	/**
	 * @param float $amountInBaseCurrency
	 * @param string|Mage_Directory_Model_Currency $customCurrency
	 * @param Df_Core_Model_StoreM|int|string|bool|null $store [optional]
	 * @return float
	 */
	public function convertFromBase($amountInBaseCurrency, $customCurrency, $store = null) {
		df_param_float($amountInBaseCurrency, 0);
		if (!is_string($customCurrency)) {
			df_assert($customCurrency instanceof Mage_Directory_Model_Currency);
			// метод ядра использует только код валюты
			$customCurrency = $customCurrency->getCode();
		}
		/**
		 * Кэшировать курсы не надо,
		 * потому что это делает системный метод @see Mage_Directory_Model_Currency::getRate()
		 */
		return df_store($store)->getBaseCurrency()->convert($amountInBaseCurrency, $customCurrency);
	}

	/**
	 * @param float $amountInBaseCurrency
	 * @param Df_Core_Model_StoreM|int|string|bool|null $store [optional]
	 * @return float
	 */
	public function convertFromBaseToHryvnias($amountInBaseCurrency, $store = null) {
		df_param_float($amountInBaseCurrency, 0);
		return df_store($store)->getBaseCurrency()->convert($amountInBaseCurrency, $this->getHryvnia());
	}

	/**
	 * @param float $amountInBaseCurrency
	 * @param Df_Core_Model_StoreM|int|string|bool|null $store [optional]
	 * @return float
	 */
	public function convertFromBaseToRoubles($amountInBaseCurrency, $store = null) {
		df_param_float($amountInBaseCurrency, 0);
		return df_store($store)->getBaseCurrency()->convert($amountInBaseCurrency, $this->getRouble());
	}

	/**
	 * @param float $amountInBaseCurrency
	 * @param Df_Core_Model_StoreM|int|string|bool|null $store [optional]
	 * @return float
	 */
	public function convertFromBaseToTenge($amountInBaseCurrency, $store = null) {
		df_param_float($amountInBaseCurrency, 0);
		return df_store($store)->getBaseCurrency()->convert($amountInBaseCurrency, $this->getTenge());
	}

	/**
	 * @param float $amountInHryvnias
	 * @param Df_Core_Model_StoreM|int|string|bool|null $store [optional]
	 * @return float
	 */
	public function convertFromHryvniasToBase($amountInHryvnias, $store = null) {
		df_param_float($amountInHryvnias, 0);
		return $this->convertToBase($amountInHryvnias, $this->getHryvnia(), $store);
	}

	/**
	 * @param float $amountInRoubles
	 * @param Df_Core_Model_StoreM|int|string|bool|null $store [optional]
	 * @return float
	 */
	public function convertFromRoublesToBase($amountInRoubles, $store = null) {
		df_param_float($amountInRoubles, 0);
		return $this->convertToBase($amountInRoubles, $this->getRouble(), $store);
	}

	/**
	 * @param float $amountInTenge
	 * @param Df_Core_Model_StoreM|int|string|bool|null $store [optional]
	 * @return float
	 */
	public function convertFromTengeToBase($amountInTenge, $store = null) {
		df_param_float($amountInTenge, 0);
		return $this->convertToBase($amountInTenge, $this->getTenge(), $store);
	}

	/**
	 * @param float $amountInCustomCurrency
	 * @param string|Mage_Directory_Model_Currency $customCurrency
	 * @param Df_Core_Model_StoreM|int|string|bool|null $store [optional]
	 * @return float
	 */
	public function convertToBase($amountInCustomCurrency, $customCurrency, $store = null) {
		df_param_float($amountInCustomCurrency, 0);
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
				(1 / df_store($store)->getBaseCurrency()->convert(doubleval(1), $customCurrency))
		;
		return $result;
	}

	/** @return Df_Directory_Model_Currency */
	public function getBase() {
		return Df_Directory_Model_Currency::ld(Mage::getStoreConfig(
			Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE
		));
	}

	/** @return Df_Directory_Model_Currency */
	public function getDollar() {
		return Df_Directory_Model_Currency::ld(Df_Directory_Model_Currency::USD);
	}

	/** @return Df_Directory_Model_Currency */
	public function getHryvnia() {
		return Df_Directory_Model_Currency::ld(Df_Directory_Model_Currency::UAH);
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