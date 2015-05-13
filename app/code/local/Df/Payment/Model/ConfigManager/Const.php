<?php
class Df_Payment_Model_ConfigManager_Const extends Df_Payment_Model_ConfigManager {
	/** @return string[] */
	public function getAllowedCurrencyCodes() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_parse_csv($this->getAllowedCurrencyCodesAsString());
			df_result_array($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/** @return string[] */
	public function getAllowedLocaleCodes() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_parse_csv($this->getAllowedLocaleCodesAsString());
			df_result_array($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/** @return mixed[] */
	public function getAvailablePaymentMethodsAsCanonicalConfigArray() {
		if (!isset($this->{__METHOD__})) {
			/** @var Mage_Core_Model_Config_Element|null $node */
			$node = $this->getNode(self::KEY__PAYMENT_METHODS, false);
			/** @var mixed[] $result */
			$result = is_null($node) ? array() : $node->asCanonicalArray();
			/**
			 * Varien_Simplexml_Element::asCanonicalArray может возвращать строку в случае,
			 * когда структура исходных данных не соответствует массиву.
			 */
			df_result_array($result);
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * Способы оплаты, предоставляемые данной платёжной системой
	 * @return array(string => array(string => string))
	 */
	public function getAvailablePaymentMethodsAsOptionArray() {
		if (!isset($this->{__METHOD__})) {
			/** @var array(string => array(string => string)) $result */
			$result = array();
			foreach ($this->getAvailablePaymentMethodsAsCanonicalConfigArray()
				as $methodCode => $methodOptions) {
				/** @var string $methodCode */
				/** @var array $methodOptions */
				df_assert_string($methodCode);
				df_assert_array($methodOptions);
				/** @var string $methodTitle */
				$methodTitle = df_a($methodOptions, self::KEY__TITLE);
				df_assert_string($methodTitle);
				$result[]=
					array(
						Df_Admin_Model_Config_Source::OPTION_KEY__LABEL => $methodTitle
						,Df_Admin_Model_Config_Source::OPTION_KEY__VALUE => $methodCode
					)
				;
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param string $key
	 * @param bool $canBeTest[optional]
	 * @return Mage_Core_Model_Config_Element|null
	 */
	public function getNode($key, $canBeTest = true) {
		df_param_string($key, 0);
		df_param_boolean($canBeTest, 1);
		if ($canBeTest) {
			$key =
				rm_config_key(
					$this->getPaymentMethod()->isTestMode() ? self::KEY__TEST : self::KEY__PRODUCTION
					,$key
				)
			;
		}
		if (!isset($this->{__METHOD__}[$key][$canBeTest])) {
			/** @var Mage_Core_Model_Config_Element|null $result */
			$result = df()->config()->getNodeByKey($this->preprocessKey($key));
			if (is_null($result)) {
				/**
				 * Пробуем получить стандартное значение параметра:
				 * из настроек модуля Df_Payment
				 */
				$result = df()->config()->getNodeByKey($this->preprocessKeyDefault($key));
			}
			$this->{__METHOD__}[$key][$canBeTest] = $result;
		}
		return $this->{__METHOD__}[$key][$canBeTest];
	}

	/**
	 * @param string $requestVar
	 * @return int
	 */
	public function getRequestVarMaxLength($requestVar) {
		df_param_string($requestVar, 0);
		if (!isset($this->{__METHOD__}[$requestVar])) {
			$this->{__METHOD__}[$requestVar] =
				rm_nat0(
					$this->getValue(
						rm_config_key(
							self::KEY__REQUEST
							,self::KEY__PAYMENT_PAGE
							,self::KEY__PARAMS
							,$requestVar
							,self::KEY__MAX_LENGTH
						)
						,$canBeTest = false
					)
				)
			;
		}
		return $this->{__METHOD__}[$requestVar];
	}

	/**
	 * @param string $key
	 * @param bool $canBeTest[optional]
	 * @param string $defaultValue[optional]
	 * @return string
	 */
	public function getUrl($key, $canBeTest = true, $defaultValue = '') {
		df_param_string($key, 0);
		df_param_boolean($canBeTest, 1);
		df_param_string($defaultValue, 2);
		/** @var string $result */
		$result = $this->getValue(rm_config_key(self::KEY__URL, $key), $canBeTest);
		df_result_string($result);
		return $result;
	}

	/**
	 * @param string $key
	 * @param bool $canBeTest
	 * @param string $defaultValue[optional]
	 * @return string
	 */
	public function getValue($key, $canBeTest, $defaultValue = '') {
		df_param_string($key, 0);
		df_param_boolean($canBeTest, 1);
		df_param_string($defaultValue, 2);
		/** @var string $result */
		$result = df()->config()->getNodeValueAsString($this->getNode($key, $canBeTest));
		if ('' === $result) {
			if ($canBeTest) {
				/**
				 * Пробуем получить значение без приставок test/production
				 */
				$result = df()->config()->getNodeValueAsString($this->getNode($key, !$canBeTest));
			}
		}
		if ('' === $result) {
			$result = $defaultValue;
		}
		$result = $this->postProcessValue($result);
		df_result_string($result);
		return $result;
	}

	/** @return bool */
	public function hasCurrencySetRestriction() {return !!$this->getAllowedCurrencyCodes();}

	/**
	 * Переводит код валюты из стандарта Magento в стандарт платёжной системы.
	 *
	 * Обратите внимание, что конкретный платёжный модуль
	 * использует либо метод translateCurrencyCode, либо метод translateCurrencyCodeReversed,
	 * но никак не оба вместе!
	 *
	 * Например, модуль WebMoney использует метод translateCurrencyCodeReversed,
	 * потому что кодам в формате платёжной системы «U» и «D»
	 * соответствует единый код в формате Magento — «USD» — и использование translateCurrencyCode
	 * просто невозможно в силу неоднозначности перевода «USD» (неясно, переводить в «U» или в «D»).
	 * @param string $currencyCodeInMagentoFormat
	 * @return string
	 */
	public function translateCurrencyCode($currencyCodeInMagentoFormat) {
		df_param_string($currencyCodeInMagentoFormat, 0);
		if (!isset($this->{__METHOD__}[$currencyCodeInMagentoFormat])) {
			/** @var string $result */
			$result =
				$this->getValue(
					rm_config_key(
						self::KEY__CURRENCIES
						,self::KEY__CODE_TRANSLATION
						,$currencyCodeInMagentoFormat
					)
					,$canBeTest = false
				)
			;
			if (!$result) {
				$result = $currencyCodeInMagentoFormat;
			}
			df_result_string($result);
			$this->{__METHOD__}[$currencyCodeInMagentoFormat] = $result;
		}
		return $this->{__METHOD__}[$currencyCodeInMagentoFormat];
	}

	/**
	 * Переводит код валюты из стандарта платёжной системы в стандарт Magento.
	 *
	 * Обратите внимание, что конкретный платёжный модуль
	 * использует либо метод translateCurrencyCode, либо метод translateCurrencyCodeReversed,
	 * но никак не оба вместе!
	 *
	 * Например, модуль WebMoney использует метод translateCurrencyCodeReversed,
	 * потому что кодам в формате платёжной системы «U» и «D»
	 * соответствует единый код в формате Magento — «USD» — и использование translateCurrencyCode
	 * просто невозможно в силу необдозначности перевода «USD» (неясно, переводить в «U» или в «D»).
	 *
	 * @param string $currencyCodeInPaymentSystemFormat
	 * @return string
	 */
	public function translateCurrencyCodeReversed($currencyCodeInPaymentSystemFormat) {
		df_param_string($currencyCodeInPaymentSystemFormat, 0);
		if (!isset($this->{__METHOD__}[$currencyCodeInPaymentSystemFormat])) {
			/** @var string $result */
			$result =
				$this->getValue(
					rm_config_key(
						self::KEY__CURRENCIES
						,self::KEY__CODE_TRANSLATION_REVERSED
						,$currencyCodeInPaymentSystemFormat
					)
					,$canBeTest = false
				)
			;
			if (!$result) {
				$result = $currencyCodeInPaymentSystemFormat;
			}
			df_result_string($result);
			$this->{__METHOD__}[$currencyCodeInPaymentSystemFormat] = $result;
		}
		return $this->{__METHOD__}[$currencyCodeInPaymentSystemFormat];
	}

	/**
	 * Переводит код локали из стандарта Magento в стандарт платёжной системы
	 * @param string $localeCodeInMagentoFormat
	 * @return string
	 */
	public function translateLocaleCode($localeCodeInMagentoFormat) {
		df_param_string($localeCodeInMagentoFormat, 0);
		if (!isset($this->{__METHOD__}[$localeCodeInMagentoFormat])) {
			/** @var string $result */
			$result =
				$this->getValue(
					rm_config_key(
						self::KEY__LOCALES
						,self::KEY__CODE_TRANSLATION
						,$localeCodeInMagentoFormat
					)
					,$canBeTest = false
				)
			;
			if (!$result) {
				$result = $localeCodeInMagentoFormat;
			}
			df_result_string($result);
			$this->{__METHOD__}[$localeCodeInMagentoFormat] = $result;
		}
		return $this->{__METHOD__}[$localeCodeInMagentoFormat];
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getKeyBase() {return self::KEY__BASE;}

	/**
	 * @param string $key
	 * @return string
	 */
	protected function preprocessKey($key) {
		df_param_string($key, 0);
		return rm_config_key(self::KEY__BASE, $this->getPaymentMethod()->getRmId(), $key);
	}

	/**
	 * @param string $key
	 * @return string
	 */
	protected function preprocessKeyDefault($key) {
		df_param_string($key, 0);
		return rm_config_key(self::KEY__BASE, self::KEY__DEFAULT, $key);
	}

	/** @return string */
	private function getAllowedCurrencyCodesAsString() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				$this->getValue(
					rm_config_key(self::KEY__CURRENCIES, self::KEY__ALLOWED)
					,$canBeTest = false
				)
			;
			df_result_string($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getAllowedLocaleCodesAsString() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				$this->getValue(
					rm_config_key(self::KEY__LOCALES, self::KEY__ALLOWED)
					,$canBeTest = false
				)
			;
		}
		return $this->{__METHOD__};
	}

	const _CLASS = __CLASS__;
	const KEY__ALLOWED = 'allowed';
	const KEY__BASE = 'df/payment';
	const KEY__CODE_TRANSLATION = 'code-translation';
	const KEY__CODE_TRANSLATION_REVERSED = 'code-translation-reversed';
	const KEY__CURRENCIES = 'currencies';
	const KEY__DEFAULT = 'default';
	const KEY__LOCALES = 'locales';
	// для getRequestVarMaxLength()
	const KEY__MAX_LENGTH = 'max_length';
	const KEY__PARAMS = 'params';
	const KEY__PAYMENT_METHODS = 'payment-methods';
	const KEY__PAYMENT_PAGE = 'payment_page';
	const KEY__PRODUCTION = 'production';
	const KEY__REQUEST = 'request';
	const KEY__TEST = 'test';
	const KEY__TITLE = 'title';
	const KEY__URL = 'url';
	/**
	 * @param Df_Payment_Model_Method_Base $paymentMethod
	 * @param Mage_Core_Model_Store $store
	 * @return Df_Payment_Model_ConfigManager_Const
	 */
	public static function i(Df_Payment_Model_Method_Base $paymentMethod, Mage_Core_Model_Store $store) {
		return new self(array(self::P__PAYMENT_METHOD => $paymentMethod, self::P__STORE => $store));
	}
}