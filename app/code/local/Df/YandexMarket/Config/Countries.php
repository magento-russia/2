<?php
class Df_YandexMarket_Config_Countries extends Df_Core_Model {
	/** @return array(string => string) */
	public function getMapFromIso2CodeToCountryNameInYandexMarketFormat() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Varien_Simplexml_Config $countriesConfigAsSimpleXml */
			$countriesConfigAsSimpleXml =
				$this->getFileAsSimpleXml()->getNode('df/yandex-market/countries/country')
			;
			df_assert($countriesConfigAsSimpleXml instanceof \Df\Xml\X);
			/** @var array(string => string) $result */
			$result = array();
			foreach ($countriesConfigAsSimpleXml as $countryConfigAsSimpleXml) {
				/** @var \Df\Xml\X $countryConfigAsSimpleXml */
				/** @var array $countryConfig */
				$countryConfig = $countryConfigAsSimpleXml->asCanonicalArray();
				/** @var string $iso2Code */
				$iso2Code = dfa($countryConfig, self::XML_TAG__CODE);
				df_assert_string($iso2Code);
				/** @var string $countryNameInYandexMarketFormat */
				$countryNameInYandexMarketFormat =
					dfa($countryConfig, self::XML_TAG__NAME__YANDEX_MARKET, '')
				;
				$result[$iso2Code] = $countryNameInYandexMarketFormat;
			}
			df_result_array($result);
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param string $nameInYandexMarketFormat
	 * @return string|null
	 */
	public function isNameValid($nameInYandexMarketFormat) {
		return in_array($nameInYandexMarketFormat, $this->getValidNames());
	}

	/** @return Df_Varien_Simplexml_Config */
	private function getFileAsSimpleXml() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Varien_Simplexml_Config $result */
			$result = new Df_Varien_Simplexml_Config();
			/** @var bool $r */
			$r = $result->loadFile($this->getFilePath('countries.xml'));
			df_assert(false !== $r);
			// Обратите внимание, что инициализируем поле _fileAsSimpleXml только сейчас,
			// после того, как убедились, что файл загрузился.
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param string $name
	 * @return string
	 */
	private function getFilePath($name) {
		return Mage::getConfig()->getModuleDir(self::FILE_DIR, self::MODULE_NAME) . DS . $name;
	}

	/** @return array(mixed => mixed) */
	private function getValidNames() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_explode_n(file_get_contents($this->getFilePath('countries.txt')));
		}
		return $this->{__METHOD__};
	}
	
	const FILE_DIR = 'etc';
	const MODULE_NAME = 'Df_YandexMarket';
	const XML_TAG__CODE = 'code';
	const XML_TAG__NAME__MAGENTO = 'magento';
	const XML_TAG__NAME__YANDEX_MARKET = 'yandex-market';

	/** @return Df_YandexMarket_Config_Countries */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}