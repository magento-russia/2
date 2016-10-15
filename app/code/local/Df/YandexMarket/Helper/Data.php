<?php
class Df_YandexMarket_Helper_Data extends Mage_Core_Helper_Data {
	/**
	 * 2015-11-07
	 * @used-by Df_YandexMarket_Model_Yml_Document::getDocumentData_Shop()
	 * @used-by Df_YandexMarket_Model_Yml_Products::applyRule()
	 * @param string|null $message
	 * @return void
	 * @throws \Df\Core\Exception
	 */
	public function error_noOffers($message = null) {
		if (!$message) {
			$message =
				'Причиной могут быть как настройки модуля «Яндекс.Маркет»,'
				. ' так и настройки товаров интернет-магазина.'
			;
		}
		/** @var string $messageFull */
		$messageFull =
			'Интернет-магазин находится в таком состоянии,'
			.' что ни один из его товаров не попадёт на Яндекс.Маркет.'
			."\n" . $message
		;
		$this->log($messageFull);
		// Всё равно файл YML будет невалидным,
		// поэтому сразу сбойно завершаем формирование этого файла.
		df_error($messageFull);
	}

	/**
	 * @param string $iso2Code
	 * @return string
	 */
	public function getCountryNameByIso2Code($iso2Code) {
		if (2 !== mb_strlen($iso2Code)) {
			/**
			 * В магазине sekretsna.com сюда вместо 2-сивольного кода страны попало значение «Турция»,
			 * потому что администраторы магазина переделали стандартное товарное свойство «country_of_manufacture»,
			 * заменив стандартный справочник стран на нестандартные текстовые названия стран.
			 * http://magento-forum.ru/index.php?app=members&module=messaging&section=view&do=showConversation&topicID=2105
			 */
			df_error('Вместо двухсимвольного кода страны система получила значение «%s».', $iso2Code);
		}
		/** @var string|null $result */
		$result = dfa(
			Df_YandexMarket_Model_Config_Countries::s()
				->getMapFromIso2CodeToCountryNameInYandexMarketFormat()
			,$iso2Code
		);
		/**
			$this->getProduct()->getAttributeText(
				Df_Catalog_Model_Product::P__COUNTRY_OF_MANUFACTURE
			)
		 * при отключенном кэшировании
		 * приводит к запросу из базы данных одного и того же списка стран
		 * для каждого товара:
			SELECT `main_table`.* FROM `directory_country` AS `main_table`
		 * Наш новый способ @uses rm_country_ctn_ru() эффективнее.
		 */
		return $result ? $result : rm_country_ctn_ru($iso2Code);
	}

	/**
	 * @param string $message
	 * @return void
	 */
	public function log($message) {
		if (df_cfg()->yandexMarket()->diagnostics()->isEnabled()) {
			/** @var mixed[] $arguments */
			$arguments = func_get_args();
			$message = df_format($arguments);
			$this->getLogger()->log($message);
		}
	}

	/** @return Df_Core_Model_Logger */
	private function getLogger() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Core_Model_Logger::s(df_file_name(
				Mage::getBaseDir('var') . DS . 'log', 'rm.yandex.market-{date}-{time}.log'
			));
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param string $url
	 * @return string
	 */
	private function preprocessDomain($url) {
		df_param_string_not_empty($url, 0);
		/** @var string $result */
		$result = $url;
		if ($this->settings()->other()->useNonStandardDomain()) {
			/** @var string $nonStandardDomain */
			$nonStandardDomain = $this->settings()->other()->getDomain();
			/** @var string $storeDomain */
			$storeDomain = Df_Core_Model_Store::s()->getDomain(rm_state()->getStoreProcessed());
			df_assert(df_contains($url, $storeDomain));
			$result = str_replace($storeDomain, $nonStandardDomain, $url);
		}
		df_result_string_not_empty($result);
		return $result;
	}

	/** @return Df_YandexMarket_Model_Settings */
	private function settings() {return df_cfg()->yandexMarket();}

	const _C = __CLASS__;
	/** @return Df_YandexMarket_Helper_Data */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}