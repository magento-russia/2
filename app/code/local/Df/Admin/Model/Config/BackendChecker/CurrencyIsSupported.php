<?php
class Df_Admin_Model_Config_BackendChecker_CurrencyIsSupported
	extends Df_Admin_Model_Config_BackendChecker {
	/**
	 * Этот метод должен быть публичным,
	 * потому что используется как callable
	 * за пределами своего класса:
	 * @see Df_Admin_Model_Config_BackendChecker_CurrencyIsSupported::getSupportMatrix()
	 *
	 * @param Mage_Core_Model_Store $store
	 * @return bool
	 */
	public function checkCurrencyIsSupportedByStore(Mage_Core_Model_Store $store) {
		/** @var bool $isAvailable */
		$isAvailable = in_array($this->getCurrencyCode(), $store->getAvailableCurrencyCodes());
		/** @var bool $hasRate */
		$hasRate = (false !== $store->getBaseCurrency()->getRate($this->getCurrencyCode()));
		if (df_is_it_my_local_pc()) {
			if (!$isAvailable) {
				Mage::log(rm_sprintf('%s is not available.', $this->getCurrencyCode()));
			}
			if (!$hasRate) {
				Mage::log(rm_sprintf('%s has no rate.', $this->getCurrencyCode()));
			}
		}
		return $isAvailable && $hasRate;
	}

	/**
	 * @override
	 * @return Df_Admin_Model_Config_BackendChecker
	 */
	protected function checkInternal() {
		if (0 < count($this->getFailedStores())) {
			df_error($this->getFailureMessage());
		}
		return $this;
	}

	/** @return Df_Directory_Model_Currency */
	private function getCurrency() {return Df_Directory_Model_Currency::ld($this->getCurrencyCode());}

	/** @return string */
	private function getCurrencyCode() {return $this->cfg(self::P__CURRENCY_CODE);}

	/** @return Zend_Currency */
	private function getCurrencyZend() {return df_zf_currency($this->getCurrencyCode());}

	/** @return string */
	private function getFailedBaseCurrenciesInFormOrigin() {
		if (!isset($this->{__METHOD__})) {
			/** @var array(string => Df_Directory_Model_Currency) $baseCurrencies */
			$baseCurrencies = array();
			foreach ($this->getFailedStores() as $failedStore) {
				/** @var Mage_Core_Model_Store $failedStore */
				/** @var Df_Directory_Model_Currency $baseCurrency */
				$baseCurrency = $failedStore->getBaseCurrency();
				if (!isset($baseCurrencies[$baseCurrency->getCode()])) {
					$baseCurrencies[$baseCurrency->getCode()] = $baseCurrency->getNameInFormOrigin();
				}
			}
			$this->{__METHOD__} = df_concat_enum($baseCurrencies);
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
			$this->{__METHOD__} =
				implode(
					' '
					,array(
						(1 === count($this->getFailedStores())) ? 'магазина' : 'магазинов'
						, $this->getFailedStores()->getNames()
					)
				)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Core_Model_Resource_Store_Collection */
	private function getFailedStores() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				Df_Core_Model_Resource_Store_Collection::i()->addIdFilter($this->getFailedStoreIds())
			;
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
					,'{currency}' => $this->getCurrencyZend()->getName()
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
			$this->{__METHOD__} =
				$this->getBackend()->getStores()->walk(array($this, 'checkCurrencyIsSupportedByStore'))
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__CURRENCY_CODE, self::V_STRING_NE);
	}
	const _CLASS = __CLASS__;
	const P__CURRENCY_CODE = 'currencyCode';
	/**
	 * @static
	 * @param string $currencyCode
	 * @param Df_Admin_Model_Config_Backend|null $backend [optional]
	 * @return Df_Admin_Model_Config_BackendChecker_CurrencyIsSupported
	 */
	public static function i($currencyCode, $backend = null) {
		return new self(array(self::P__CURRENCY_CODE => $currencyCode, self::P__BACKEND => $backend));
	}
}