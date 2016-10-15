<?php
class Df_YandexMarket_Model_Yml_Document extends Df_Catalog_Model_XmlExport_Catalog {
	/**
	 * @override
	 * @see \Df\Xml\Generator\Document::getExportCurrency()
	 * @return Df_Directory_Model_Currency
	 */
	public function getExportCurrency() {return $this->getSettings()->general()->getCurrency();}

	/**
	 * @override
	 * @return string
	 */
	public function getOperationNameInPrepositionalCase() {return 'формировании документа YML';}

	/**
	 * @override
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
		$scheme = df_first($schemeAndOther);
		/** @var string $schemeTail */
		/**
		 * Раньше тут стояло:
		 * $schemeTail = df_last($schemeAndOther).
		 * Причина смены кода объяснена в предыдущем комментарии выше.
		 */
		$schemeTail = implode('//', df_tail($schemeAndOther));
		/** @var string[] $schemeTailExploded */
		$schemeTailExploded = df_explode_url($schemeTail);
		df_assert_ge(2, count($schemeTailExploded));
		/** @var string $domainAndPort */
		$domainAndPort = df_first($schemeTailExploded);
		if (df_cfg()->yandexMarket()->other()->useNonStandardDomain()) {
			$domainAndPort = df_cfg()->yandexMarket()->other()->getDomain();
		}
		/** @var string[] $path */
		$pathExploded = df_tail($schemeTailExploded);
		/** @var string $resultRaw */
		$result =
			$scheme . '//' . $domainAndPort . '/' . df_cc_path(array_map('urlencode', $pathExploded))
		;
		return $result;
	}

	/**
	 * @override
	 * @return array(string => string)
	 */
	protected function getAttributes() {
		return array('date' => df_dts(Zend_Date::now(), 'y-MM-dd HH:mm'));
	}

	/**
	 * @override
	 * @return array(string => mixed)
	 */
	protected function getContentsAsArray() {return array('shop' => $this->getOutput_Shop());}

	/**
	 * @override
	 * @return string
	 */
	protected function getDocType() {return "<!DOCTYPE yml_catalog SYSTEM 'shops.dtd'>";}

	/**
	 * Намеренно возвращаем пустое значение!
	 * @override
	 * @return string
	 */
	protected function getLogDocumentName() {return '';}

	/**
	 * @override
	 * @return string
	 */
	protected function getProcessorClass_products() {return Df_YandexMarket_Model_Yml_Processor_Offer::class;}

	/**
	 * @override
	 * @return string
	 */
	protected function getTagName() {return 'yml_catalog';}

	/**
	 * @override
	 * @return bool
	 */
	protected function needLog() {return df_cfg()->yandexMarket()->diagnostics()->isEnabled();}
	
	/** @return array(array(string => string|array(string => int))) */
	private function getOutput_Categories() {
		if (!isset($this->{__METHOD__})) {
			/** @var array(array(string => string|array(string => int))) $result  */
			$result = array();
			foreach ($this->getCategories() as $category) {
				/** @var Df_Catalog_Model_Category $category */
				if ($category->getId()) {
					/** @var array(string => int) $attributes */
					$attributes = array('id' => $category->getId());
					if (0 < $category->getParentId()) {
						$attributes['parentId'] = $category->getParentId();
					}
					$result[]= array(
						\Df\Xml\X::ATTR => $attributes
						,\Df\Xml\X::CONTENT => df_cdata(
							$category->getName() ? $category->getName() : $category->getId()
						)
					);
				}
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return array */
	private function getOutput_Currencies() {
		/** @var array $result */
		$result = array(array(\Df\Xml\X::ATTR => array(
			'id' => $this->getSettings()->general()->getCurrencyCode()
			/**
			 * Параметр rate указывает курс валюты к курсу основной валюты,
			 * взятой за единицу (валюта, для которой rate="1").
			 *
			 * В качестве основной валюты (для которой установлено rate="1")
			 * могут быть использованы только рубль (RUR, RUB),
			 * белорусский рубль (BYR),
			 * гривна (UAH)
			 * или тенге (KZT).
			 */
			,'rate' => 1
		)));
		return $result;
	}

	/** @return array */
	private function getOutput_Shop() {
		/** @var array(string => mixed) $result */
		$result = array(
			'name' => $this->getSettings()->shop()->getNameForClients()
			,'company' => $this->getSettings()->shop()->getNameForAdministration()
			,'url' => $this->preprocessUrl(
				df_state()->getStoreProcessed()->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB)
			)
			,'platform' => 'Российская сборка Magento'
			,'version' => df_version()
			,'agency' => $this->getSettings()->shop()->getAgency()
			,'email' => $this->getSettings()->shop()->getSupportEmail()
			,'currencies' => array('currency' => $this->getOutput_Currencies())
			,'categories' => array('category' => $this->getOutput_Categories())
		);
		/** http://magento-forum.ru/topic/4201/ */
		if (!$this->getOutput_Products()) {
			df_h()->yandexMarket()->error_noOffers();
		}
		/** http://magento-forum.ru/topic/4201/ */
		if (!$this->getOutput_Categories()) {
			/** @var string $message */
			$message =
				'Ни один из передаваемых на Яндекс.Маркет товаров'
				.' не привязан ни к одному товарному разделу. '
				."\nКаждый передаваемый на Яндекс.Маркет товар"
				.' должен быть привязан хотя бы к одному товарному разделу.'
			;
			$this->notify($message);
			// Всё равно файл YML будет невалидным,
			// поэтому сразу сбойно завершаем формирование этого файла.
			df_error($message);
		}
		if (0 !== $this->getSettings()->general()->getLocalDeliveryCost()) {
			$result['local_delivery_cost'] = $this->getSettings()->general()->getLocalDeliveryCost();
		}
		$result['offers'] = array('offer' => $this->getOutput_Products());
		return $result;
	}

	/** @return Df_YandexMarket_Model_Settings */
	private function getSettings() {return Df_YandexMarket_Model_Settings::s();}

	/**
	 * @used-by Df_YandexMarket_Model_Action_Front::getDocument()
	 * @param Df_Catalog_Model_Resource_Product_Collection $products
	 * @return Df_YandexMarket_Model_Yml_Document
	 */
	public static function i(Df_Catalog_Model_Resource_Product_Collection $products) {
		return self::ic(__CLASS__, $products);
	}
}