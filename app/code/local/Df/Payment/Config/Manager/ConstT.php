<?php
namespace Df\Payment\Config\Manager;
class ConstT extends \Df\Payment\Config\ManagerBase {
	/** @return string[] */
	public function allowedCurrencyCodes() {return dfc($this, function() {
		/** @var string $resultS */
		$resultS = $this->getValue(df_cc_path(self::$KEY__CURRENCIES, self::$KEY__ALLOWED));
		if (!df_check_string_not_empty($resultS)) {
			df_error(
				'Программисту платёжного модуля «%s» требуется указать допустимые валюты.'
				,df_module_name($this->main())
			);
		}
		return df_csv_parse($resultS);
	});}

	/** @return string[] */
	public function allowedLocaleCodes() {return dfc($this, function() {return
		df_csv_parse($this->getValue(df_cc_path(self::$KEY__LOCALES, self::$KEY__ALLOWED)))
	;});}

	/** @return mixed[] */
	public function methodsCA() {return dfc($this, function() {return
		df_config_a($this->getNode('payment-methods'))
	;});}

	/**
	 * Способы оплаты, предоставляемые данной платёжной системой
	 * @return array(string => array(string => string))
	 */
	public function availablePaymentMethodsAsOptionArray() {return dfc($this, function() {
		/** @var array(string => array(string => string)) $result */
		$result = [];
		foreach ($this->methodsCA() as $methodCode => $methodOptions) {
			/** @var string $methodCode */
			/** @var array $methodOptions */
			df_assert_string($methodCode);
			df_assert_array($methodOptions);
			/** @var string $methodTitle */
			$methodTitle = dfa($methodOptions, 'title');
			df_assert_string($methodTitle);
			$result[]= df_option($methodCode, $methodTitle);
		}
		return $result;
	});}

	/**
	 * @used-by \Df\Payment\Config\Area::getConst()
	 * @param string $key
	 * @param bool $canBeTest [optional]
	 * @param string $default [optional]
	 * @return string
	 */
	public function const_($key, $canBeTest = true, $default = '') {return
		$canBeTest ? $this->getValueT($key, $default) : $this->getValue($key, $default)
	;}

	/**
	 * @param string $v
	 * @return int
	 */
	public function requestVarMaxLength($v) {return dfc($this, function($v) {return
		df_nat0($this->getValue("request/payment_page/params/{$v}/"))
	;}, func_get_args());}

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
	public function hasCurrencySetRestriction() {return !!$this->allowedCurrencyCodes();}

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
	 * @param string $c
	 * @return string
	 */
	public function translateCurrencyCode($c) {return dfc($this, function($c) {return
		$this->getValue(df_cc_path(self::$KEY__CURRENCIES, self::$KEY__CODE_TRANSLATION, $c)) ?: $c
	;}, func_get_args());}

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
	 * @param string $c
	 * @return string
	 */
	public function translateCurrencyCodeReversed($c) {return dfc($this, function($c) {return
		$this->getValue(df_cc_path(self::$KEY__CURRENCIES, 'code-translation-reversed', $c)) ?: $c
	;}, func_get_args());}

	/**
	 * Переводит код локали из стандарта Magento в стандарт платёжной системы
	 * @param string $c
	 * @return string
	 */
	public function translateLocaleCode($c) {return dfc($this, function($c) {return
		$this->getValue(df_cc_path(self::$KEY__LOCALES, self::$KEY__CODE_TRANSLATION, $c)) ?: $c
	;}, func_get_args());}

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

	/**
	 * @see isDefault()
	 * @return \Df\Payment\Config\Manager\ConstT\DefaultT
	 */
	private function getDefault() {return \Df\Payment\Config\Manager\ConstT\DefaultT::s($this->main());}

	/** @return \Df\Payment\Config\Manager\ConstT\ModeSpecific */
	private function getModeSpecific() {return
		\Df\Payment\Config\Manager\ConstT\ModeSpecific::s($this->main())
	;}

	/**
	 * @override
	 * @param string $key
	 * @return \Mage_Core_Model_Config_Element|null
	 */
	private function getNode($key) {return
		df_config_node($this->adaptKey($key)) ?:
			($this->isDefault() ? null : $this->getDefault()->getNode($key))
	;}

	/**
	 * @override
	 * @param string $key
	 * @return \Mage_Core_Model_Config_Element|null
	 */
	private function getNodeT($key) {return
		$this->getModeSpecific()->getNode($key) ?: $this->getNode($key)
	;}

	/**
	 * 2016-10-18
	 * Не очень красивое, временное решение.
	 * @see getDefault()
	 * @used-by getNode()
	 * @return bool
	 */
	private function isDefault() {return $this instanceof \Df\Payment\Config\Manager\ConstT\DefaultT;}

	/** @var string */
	private static $KEY__ALLOWED = 'allowed';
	/** @var string */
	private static $KEY__CODE_TRANSLATION = 'code-translation';
	/** @var string */
	private static $KEY__CURRENCIES = 'currencies';
	/** @var string */
	private static $KEY__LOCALES = 'locales';

	/**
	 * @used-by \Df\Payment\Method::constManager()
	 * @param \Df\Payment\Method|\Df\Checkout\Module\Main $method
	 * 2016-10-18
	 * Тип параметра — всегда @see \Df\Payment\Method,
	 * но в сигнатуре вынуждены указать @see \Df\Checkout\Module\Main
	 * для совместимости с унаследованным методом @see \Df\Checkout\Module\Config\Manager::s()
	 * @return self
	 */
	public static function s(\Df\Checkout\Module\Main $method) {return self::sc(__CLASS__, $method);}
}