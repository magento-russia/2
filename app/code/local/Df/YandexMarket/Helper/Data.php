<?php
class Df_YandexMarket_Helper_Data extends Mage_Core_Helper_Data {
	/**
	 * 2015-11-07
	 * @used-by Df_YandexMarket_Model_Yml_Document::getDocumentData_Shop()
	 * @used-by Df_YandexMarket_Product_Exporter::noMatchingProductIds()
	 * @param string|null $message
	 * @return void
	 * @throws Df_Core_Exception_Client
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
		$this->notify($messageFull);
		// Всё равно файл YML будет невалидным,
		// поэтому сразу сбойно завершаем формирование этого файла.
		df_error($messageFull);
	}

	/**
	 * @param string|float $money
	 * @return string
	 */
	public function formatMoney($money) {return rm_sprintf(rm_float($money), '.2f');}

	/**
	 * @param string $iso2Code
	 * @return string
	 */
	public function getCountryNameByIso2Code($iso2Code) {
		if (2 !== strlen($iso2Code)) {
			/**
			 * В магазине sekretsna.com сюда вместо 2-сивольного кода страны попало значение «Турция»,
			 * потому что администраторы магазина переделали стандартное товарное свойство «country_of_manufacture»,
			 * заменив стандартный справочник стран на нестандартные текстовые названия стран.
			 * @link http://magento-forum.ru/index.php?app=members&module=messaging&section=view&do=showConversation&topicID=2105
			 */
			df_error('Вместо двухсимвольного кода страны система получила значение «%s».', $iso2Code);
		}
		/** @var string|null $result */
		$result =
			df_a(
				Df_YandexMarket_Model_Config_Countries::s()
					->getMapFromIso2CodeToCountryNameInYandexMarketFormat()
				,$iso2Code
			)
		;
		if (!$result) {
			/** @var string $result */
			$result =
				df_nts(
					/**
						$this->getProduct()->getAttributeText(
							Df_Catalog_Model_Product::P__COUNTRY_OF_MANUFACTURE
						)
					 * при отключенном кэшировании
					 * приводит к запросу из базы данных одного и того же списка стран
					 * для каждого товара:
					 *
						SELECT `main_table`.* FROM `directory_country` AS `main_table`
					 *
					 */
					df_h()->directory()->country()->getLocalizedNameByIso2Code($iso2Code, 'ru_RU')
				)
			;
		}
		return $result;
	}

	/**
	 * @param mixed $message
	 * @return Df_YandexMarket_Helper_Data
	 */
	public function notify($message) {
		/**
		 * Обратите внимание,
		 * что функция func_get_args() не может быть параметром другой функции.
		 */
		$arguments = func_get_args();
		$message = rm_sprintf($arguments);
		Mage::log($message, $level = null, $file = 'rm.yandex.market.log', $forceLog = true);
		if (!df_is_it_my_local_pc() && $this->settings()->general()->getNotificationEmail()) {
			Df_Qa_Message_Notification::i(array(
				Df_Qa_Message_Notification::P__NOTIFICATION => $message
				,Df_Qa_Message_Notification::P__NEED_LOG_TO_FILE => false
				,Df_Qa_Message_Notification::P__NEED_NOTIFY_DEVELOPER => false
				,Df_Qa_Message_Notification::P__RECIPIENTS =>
					array($this->settings()->general()->getNotificationEmail())
			))->log();
		}
		return $this;
	}

	/**
	 * @param string $url
	 * @return string
	 */
	public function preprocessUrl($url) {
		/** @var array $schemeAndOther */
		$schemeAndOther = explode('//', $url);
		/**
		 * Раньше я считал, что count($schemeAndOther) должно быть всегда равно 2.
		 * Однако потом заметил, что в магазине sekretsna.com
		 * $product->getProductUrl($useSid = false)
		 * возвращает значения вроде «http://sekretsna.com//la-scala-bpr-12-semejnoe-160x220x2.html»,
		 * то есть, с лишним  символом «/» после имени домена.
		 *
		 * В магазине sekretsna.com подобные веб-адреса образовались
		 * в результате ручных или программных правок справочника перенаправлений.
		 * Причём подобные адреса — рабочие.
		 * Более того, Magento почему-то даже открывает тот же самый товар при трёх символах «/» подряд:
		 * «http://sekretsna.com///la-scala-bpr-12-semejnoe-160x220x2.html».
		 *
		 * Конечно, подобные адреса не вполне хороши
		 * и могут приводить к дубликатам страниц для поисковых систем,
		 * однако модуля Яндекс.Маркет это не касается и он падать из-за таких адресов не должен.
		 */
		if (2 > count($schemeAndOther)) {
			df_error('Система не смогла распознать значение «%s» как веб-адрес.', $url);
		}
		/** @var string $scheme */
		$scheme = rm_first($schemeAndOther);
		/** @var string $schemeTail */
		/**
		 * Раньше тут стояло:
		 * $schemeTail = rm_last($schemeAndOther).
		 * Причина смены кода объяснена в предыдущем комментарии выше.
		 */
		$schemeTail = implode('//', rm_tail($schemeAndOther));
		/** @var string[] $schemeTailExploded */
		$schemeTailExploded = explode('/', $schemeTail);
		df_assert_ge(2, count($schemeTailExploded));
		/** @var string $domainAndPort */
		$domainAndPort = rm_first($schemeTailExploded);
		if ($this->settings()->other()->useNonStandardDomain()) {
			$domainAndPort = $this->settings()->other()->getDomain();
		}
		/** @var string[] $path */
		$pathExploded= rm_tail($schemeTailExploded);
		/** @var string $resultRaw */
		$result =
			$scheme . '//' . $domainAndPort . '/' . df_concat_url(array_map('urlencode', $pathExploded))
		;
		return $result;
	}

	/**
	 * @param string $message
	 * @return Df_YandexMarket_Helper_Data
	 */
	public function log($message) {
		if (df_cfg()->yandexMarket()->diagnostics()->isEnabled()) {
			/** @var mixed[] $arguments */
			$arguments = func_get_args();
			$message = rm_sprintf($arguments);
			$this->getLogger()->log($message);
		}
		return $this;
	}

	/** @return Df_Core_Model_Logger */
	private function getLogger() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				Df_Core_Model_Logger::s(array(
					Df_Core_Model_Logger::P__FILE_NAME =>
						basename(Df_Core_Model_Fs_GetNotUsedFileName::r(
							Mage::getBaseDir('var') . DS . 'log'
							, 'rm.yandex.market-{date}-{time}.log'
						))
					, Df_Core_Model_Logger::P__FORMATTER => new Df_Zf_Log_Formatter_Benchmark()
				))
			;
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
			df_assert(rm_contains($url, $storeDomain));
			$result = str_replace($storeDomain, $nonStandardDomain, $url);
		}
		df_result_string_not_empty($result);
		return $result;
	}

	/** @return Df_YandexMarket_Model_Settings */
	private function settings() {return df_cfg()->yandexMarket();}

	const _CLASS = __CLASS__;
	/** @return Df_YandexMarket_Helper_Data */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}