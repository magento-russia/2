<?php
class Df_Admin_Config_BackendChecker_CurrencyIsSupported
	extends Df_Admin_Config_BackendChecker {
	/**
	 * Этот метод должен быть публичным,
	 * потому что используется как callable за пределами своего класса:
	 * @used-by Df_Admin_Config_BackendChecker_CurrencyIsSupported::getSupportMatrix()
	 * @param Df_Core_Model_StoreM $store
	 * @return bool
	 */
	public function checkCurrencyIsSupportedByStore(Df_Core_Model_StoreM $store) {
		/** @var bool $isAvailable */
		$isAvailable = in_array($this->getCurrencyCode(), $store->getAvailableCurrencyCodes());
		/** @var bool $hasRate */
		$hasRate = (false !== $store->getBaseCurrency()->getRate($this->getCurrencyCode()));
		if (df_my_local()) {
			if (!$isAvailable) {
				Mage::log(sprintf('%s is not available.', $this->getCurrencyCode()));
			}
			if (!$hasRate) {
				Mage::log(sprintf('%s has no rate.', $this->getCurrencyCode()));
			}
		}
		return $isAvailable && $hasRate;
	}

	/**
	 * @override
	 * @return Df_Admin_Config_BackendChecker
	 */
	protected function checkInternal() {
		if ($this->getFailedStores()->count()) {
			df_error($this->getFailureMessage());
		}
		return $this;
	}

	/** @return Df_Directory_Model_Currency */
	private function getCurrency() {return df_currency($this->getCurrencyCode());}

	/** @return string */
	private function getCurrencyCode() {return $this->cfg(self::$P__CURRENCY_CODE);}

	/** @return string */
	private function getFailedBaseCurrenciesInFormOrigin() {
		if (!isset($this->{__METHOD__})) {
			/** @var array(string => Df_Directory_Model_Currency) $baseCurrencies */
			$baseCurrencies = array();
			foreach ($this->getFailedStores() as $failedStore) {
				/** @var Df_Core_Model_StoreM $failedStore */
				/** @var Df_Directory_Model_Currency $baseCurrency */
				$baseCurrency = $failedStore->getBaseCurrency();
				if (!isset($baseCurrencies[$baseCurrency->getCode()])) {
					$baseCurrencies[$baseCurrency->getCode()] = $baseCurrency->getNameInFormOrigin();
				}
			}
			$this->{__METHOD__} = df_csv_pretty($baseCurrencies);
		}
		return $this->{__METHOD__};
	}

	/** @return int[] */
	private function getFailedStoreIds() {
		if (!isset($this->{__METHOD__})) {
			/** @var int[] $result */
			$this->{__METHOD__} =
				array_diff(
					array_keys($this->getSupportMatrix())
					,array_keys(array_filter($this->getSupportMatrix()))
				)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return string[] */
	private function getFailedStoreNames() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = implode(' ', array(
				(1 === $this->getFailedStores()->count()) ? 'магазина' : 'магазинов'
				, $this->getFailedStores()->getNames()
			));
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Core_Model_Resource_Store_Collection */
	private function getFailedStores() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Core_Model_Store::c()->addIdFilter($this->getFailedStoreIds());
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getFailureMessage() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = strtr(
				$this->getFailureMessageTemplate()
				,array(
					'{module}' => $this->getBackend()->getModuleName()
					,'{baseCurrenciesOrigin}' => $this->getFailedBaseCurrenciesInFormOrigin()
					,'{currency}' => df_currency_name($this->getCurrencyCode())
					,'{currencyInstrumental}' => $this->getCurrency()->getNameInCaseInstrumental()
					,'{stores}' => $this->getFailedStoreNames()
					/**
					 * Раньше с формой «куда» была проблема:
					 * для казахского тенге Морфер возвращал «на казахского тенге».
					 * Однако это вроде исправлено:
					 * http://morpher.ru/WebService.aspx#msg2515
					 */
					,'{currencyInFormDestionation}' => $this->getCurrency()->getNameInFormDestination()
				)
			);
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getFailureMessageTemplate() {
		return
		 	$this->getBackend()->getFailureMessageTemplate()
			? $this->getBackend()->getFailureMessageTemplate()
			: 'Модулю «{module}» нужно работать с {currencyInstrumental}.<br/>'
			.'1) Убедитесь, что валюта «{currency}» включена для {stores}.<br/>'
			/**
			 * Раньше с формой «куда» была проблема:
			 * для казахского тенге Морфер возвращал «на казахского тенге».
			 * Однако это вроде исправлено:
			 * http://morpher.ru/WebService.aspx#msg2515
			 */
			.'2) Укажите курс обмена {baseCurrenciesOrigin} на {currencyInFormDestionation}.'
		;
	}

	/** @return bool[] */
	private function getSupportMatrix() {
		if (!isset($this->{__METHOD__})) {
			/** @uses checkCurrencyIsSupportedByStore() */
			$this->{__METHOD__} = $this->getBackend()->getStores()->walk(array(
				$this, 'checkCurrencyIsSupportedByStore'
			));
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__CURRENCY_CODE, DF_V_STRING_NE);
	}
	/** @var string */
	private static $P__CURRENCY_CODE = 'currencyCode';
	/**
	 * @static
	 * @param string $currencyCode
	 * @param Df_Admin_Config_Backend $backend
	 * @return void
	 */
	public static function _check(Df_Admin_Config_Backend $backend, $currencyCode) {
		/** @var Df_Admin_Config_BackendChecker_CurrencyIsSupported $checker */
		$checker = new self(array(self::$P__CURRENCY_CODE => $currencyCode, self::$P__BACKEND => $backend));
		$checker->check();
	}
}