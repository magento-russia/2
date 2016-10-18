<?php
class Df_Payment_Config_Manager_Const extends Df_Payment_Config_ManagerBase {
	/** @return string[] */
	public function getAllowedCurrencyCodes() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_csv_parse($this->getAllowedCurrencyCodesAsString());
		}
		return $this->{__METHOD__};
	}

	/** @return string[] */
	public function getAllowedLocaleCodes() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_csv_parse($this->getAllowedLocaleCodesAsString());
		}
		return $this->{__METHOD__};
	}

	/** @return mixed[] */
	public function getAvailablePaymentMethodsAsCanonicalConfigArray() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_config_a($this->getNode('payment-methods'));
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
				$methodTitle = dfa($methodOptions, 'title');
				df_assert_string($methodTitle);
				$result[]= df_option($methodCode, $methodTitle);
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @used-by Df_Payment_Config_Area::getConst()
	 * @param string $key
	 * @param bool $canBeTest [optional]
	 * @param string $default [optional]
	 * @return string
	 */
	public function getConst($key, $canBeTest = true, $default = '') {
		return $canBeTest ? $this->getValueT($key, $default) : $this->getValue($key, $default);
	}

	/**
	 * @param string $requestVar
	 * @return int
	 */
	public function getRequestVarMaxLength($requestVar) {
		df_param_string($requestVar, 0);
		if (!isset($this->{__METHOD__}[$requestVar])) {
			$this->{__METHOD__}[$requestVar] = df_nat0($this->getValue(df_cc_path(
				'request/payment_page/params', $requestVar, 'max_length'
			)));
		}
		return $this->{__METHOD__}[$requestVar];
	}

	/**
	 * @param string $key
	 * @param bool $canBeTest [optional]
	 * @param string $default [optional]
	 * @return string
	 */
	public function getUrl($key, $canBeTest = true, $default = '') {
		/** @var string $key */
		$key = df_cc_path('url', $key);
		return $canBeTest ? $this->getValueT($key, $default) : $this->getValue($key, $default);
	}

	/**
	 * @override
	 * @param string $key
	 * @param mixed $default [optional]
	 * @return mixed
	 */
	public function getValue($key, $default = null) {return df_leaf($this->getNode($key), $default);}

	/**
	 * @param string $key
	 * @param string $default [optional]
	 * @return string
	 */
	public function getValueT($key, $default = '') {return df_leaf($this->getNodeT($key), $default);}

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
			$result = $this->getValue(df_cc_path(
				self::$KEY__CURRENCIES, self::$KEY__CODE_TRANSLATION, $currencyCodeInMagentoFormat
			));
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
			$result = $this->getValue(df_cc_path(
				self::$KEY__CURRENCIES, 'code-translation-reversed', $currencyCodeInPaymentSystemFormat
			));
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
			$result = $this->getValue(df_cc_path(
				self::$KEY__LOCALES, self::$KEY__CODE_TRANSLATION, $localeCodeInMagentoFormat
			));
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
	 * @param string $key
	 * @return string|null
	 */
	protected function _getValue($key) {return df_leaf_s(df_config_node($key));}

	/**
	 * @override
	 * @return string
	 */
	protected function getKeyBase() {return 'df/payment';}

	/** @return string */
	private function getAllowedCurrencyCodesAsString() {return dfc($this, function() {
		/** @var string $result */
		$result = $this->getValue(df_cc_path(self::$KEY__CURRENCIES, self::$KEY__ALLOWED));
		if (!df_check_string_not_empty($result)) {
			df_error(
				'Программисту платёжного модуля «%s» требуется указать допустимые валюты.'
				,df_module_name($this->main())
			);
		}
	});}

	/** @return string */
	private function getAllowedLocaleCodesAsString() {return dfc($this, function() {return
		$this->getValue(df_cc_path(self::$KEY__LOCALES, self::$KEY__ALLOWED))
	;});}

	/**
	 * @see isDefault()
	 * @return Df_Payment_Config_Manager_Const_Default
	 */
	private function getDefault() {return Df_Payment_Config_Manager_Const_Default::s($this->main());}

	/** @return Df_Payment_Config_Manager_Const_ModeSpecific */
	private function getModeSpecific() {return
		Df_Payment_Config_Manager_Const_ModeSpecific::s($this->main())
	;}

	/**
	 * @override
	 * @param string $key
	 * @return Mage_Core_Model_Config_Element|null
	 */
	private function getNode($key) {
		/** @var Mage_Core_Model_Config_Element|null $result */
		$result = df_config_node($this->adaptKey($key));
		/**
		 * 2015-08-04
		 * Раньше тут стояло
		 * $result && df_xml_exists($result)
		 * что, видимо, неправильно, потому что @see df_xml_exists()
		 * возвращает false для текстовых узлов.
		 */
		return $result ?: ($this->isDefault() ? null : $this->getDefault()->getNode($key));
	}

	/**
	 * @override
	 * @param string $key
	 * @return Mage_Core_Model_Config_Element|null
	 */
	private function getNodeT($key) {
		/** @var Mage_Core_Model_Config_Element|null $result */
		$result = $this->getModeSpecific()->getNode($key);
		/**
		 * 2015-08-04
		 * Раньше тут стояло
		 * $result && df_xml_exists($result)
		 * что, видимо, неправильно, потому что @see df_xml_exists()
		 * возвращает false для текстовых узлов.
		 */
		return $result ? $result : $this->getNode($key);
	}

	/**
	 * 2016-10-18
	 * Не очень красивое, временное решение.
	 * @see getDefault()
	 * @used-by getNode()
	 * @return bool
	 */
	private function isDefault() {return $this instanceof Df_Payment_Config_Manager_Const_Default;}

	/** @var string */
	private static $KEY__ALLOWED = 'allowed';
	/** @var string */
	private static $KEY__CODE_TRANSLATION = 'code-translation';
	/** @var string */
	private static $KEY__CURRENCIES = 'currencies';
	/** @var string */
	private static $KEY__LOCALES = 'locales';

	/**
	 * @used-by Df_Payment_Model_Method::constManager()
	 * @param Df_Payment_Model_Method|Df_Checkout_Module_Main $method
	 * 2016-10-18
	 * Тип параметра — всегда @see Df_Payment_Model_Method,
	 * но в сигнатуре вынуждены указать @see Df_Checkout_Module_Main
	 * для совместимости с унаследованным методом @see Df_Checkout_Module_Config_Manager::s()
	 * @return Df_Payment_Config_Manager_Const
	 */
	public static function s(Df_Checkout_Module_Main $method) {return self::sc(__CLASS__, $method);}
}