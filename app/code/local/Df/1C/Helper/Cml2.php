<?php
class Df_1C_Helper_Cml2 extends Mage_Core_Helper_Abstract {
	/** @return Df_1C_Helper_Cml2_AttributeSet */
	public function attributeSet() {
		return Df_1C_Helper_Cml2_AttributeSet::s();
	}

	/**
	 * @param string $currencyCodeInMagentoFormat
	 * @return string
	 */
	public function convertCurrencyCodeTo1CFormat($currencyCodeInMagentoFormat) {
		df_param_string_not_empty($currencyCodeInMagentoFormat, 0);
		$result =
			df_a(
				df_cfg()->_1c()->general()->getCurrencyCodesMapFromMagentoTo1C()
				,$currencyCodeInMagentoFormat
				,$currencyCodeInMagentoFormat
			)
		;
		/**
		 * Раньше тут стояло df_result_string_not_empty,
		 * однако в магазине belle.com.ua это привело к сбою:
		 * [Df_1C_Helper_Cml2::convertCurrencyCodeTo1CFormat]
		   Результат метода забракован проверяющим «df_result_string».
		   Сообщения проверяющего:
		   Требуется строка, но вместо неё получена переменная типа «integer».
		 * Видимо, это потому, что код может быть числовым, например: 960.
		 * Поэтому вместо df_result_string_not_empty используем просто !
		 * @link http://magento-forum.ru/topic/3704/
		 */
		if (!$result) {
			df_error('Не могу перевести в формат 1С валютный код «%s».', $currencyCodeInMagentoFormat);
		}
		return $result;
	}

	/**
	 * @param string $currencyCodeIn1CFormat
	 * @return string
	 */
	public function convertCurrencyCodeToMagentoFormat($currencyCodeIn1CFormat) {
		df_param_string_not_empty($currencyCodeIn1CFormat, 0);
		/** @var $codeNormalized $result */
		$codeNormalized = $this->normalizeNonStandardCurrencyCode($currencyCodeIn1CFormat);
		$result =
			df_a(
				array_merge(
					array(
						'РУБ' => 'RUB'
						,'ГРН' => 'UAH'
					)
					,df_cfg()->_1c()->general()->getCurrencyCodesMapFrom1CToMagento()
				)
				,$codeNormalized
				,$codeNormalized
			)
		;
		df_result_string_not_empty($result);
		return $result;
	}

	/**
	 * @param $string|null
	 * @return bool
	 */
	public function isExternalId($string) {
		/**
		 * пример внешнего идентификатора: 6cc37c6d-7d15-11df-901f-00e04c595000
		 */
		/** @var bool $result */
		$result =
				is_string($string)
			&&
				(36 === mb_strlen($string))
			&&
				(5 === count(explode('-', $string)))
		;
		return $result;
	}

	/** @return bool */
	public function isItCml2Processing() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
					class_exists('Df_1C_Cml2Controller', $autoload = false)
				&&
					(rm_state()->getController() instanceof Df_1C_Cml2Controller)
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param string $nonStandardCurrencyCode
	 * @return string
	 */
	public function normalizeNonStandardCurrencyCode($nonStandardCurrencyCode) {
		df_param_string_not_empty($nonStandardCurrencyCode, 0);
		/** @var string $result */
		$result =
			mb_substr(
				df_trim(
					mb_strtoupper(
						$nonStandardCurrencyCode
					)
					,' .'
				)
				,0
				,3
			)
		;
		df_result_string_not_empty($result);
		return $result;
	}

	/**
	 * @param Df_Catalog_Model_Product $product
	 * @return void
	 */
	public function reindexProduct(Df_Catalog_Model_Product $product) {
		$product
			->reindexPrices()
			->reindexStockStatus()
			->reindexUrlRewrites()
		;
	}

	/**
	 * @param string $path
	 * @param string $value
	 * @return Df_1C_Helper_Cml2
	 */
	public function setStoreProcessedConfigValue($path, $value) {
		Mage::getConfig()->saveConfig(
			$path
			,$value
			,$scope = 'stores'
			,$scopeId = rm_state()->getStoreProcessed()->getId()
		);
		Mage::app()->getStore()->setConfig($path, $value);
		return $this;
	}

	/** @return Df_1C_Helper_Cml2 */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}