<?php
/**
 * @method Df_YandexMarket_Yml_Document getDocument()
 */
class Df_YandexMarket_Yml_Processor_Offer extends Df_Catalog_Model_XmlExport_Product {
	/**
	 * @override
	 * @return array(string => mixed)
	 */
	public function getResult() {
		/** @var array(string => mixed) $attributes */
		$attributes = array(
			'id' => $this->getProduct()->getId()
			,'available' => df_bts($this->getProduct()->isInStock())
		);
		if ($this->hasVendorInfo()) {
			$attributes['type'] = 'vendor.model';
		}
		return array(\Df\Xml\X::ATTR => $attributes, \Df\Xml\X::CONTENT => $this->getValue());
	}

	/**
	 * @override
	 * @return bool
	 */
	public function isEligible() {
		/** @var bool $result */
		$result = false;
		/**
		 * Не передаём на Яндекс.Маркет товары типов:
		 * Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE
		 * Mage_Catalog_Model_Product_Type::TYPE_GROUPED
		 * Вместо этого на Яндекс.Маркет будут переданы входящие в их состав простые товары.
		 *
		 * Обратите внимание, что в то же время передаём на Яндекс.Маркет товары типа
		 * Mage_Catalog_Model_Product_Type::TYPE_BUNDLE (товарные комплекты)
		 */
		/** @var string[] $eligibleTypes */
		static $eligibleTypes = array(
			Mage_Catalog_Model_Product_Type::TYPE_SIMPLE
			,Mage_Catalog_Model_Product_Type::TYPE_VIRTUAL
			,Mage_Catalog_Model_Product_Type::TYPE_BUNDLE
		);
		if (!in_array($this->getProduct()->getTypeId(), $eligibleTypes)) {
			$this->log(
				'Товару %s отказано в публикации, потому что он типа «%s»,'
				.' а публикуются только товары типов «простой», «виртуальный», «комплект».'
				,$this->getProduct()->getTitle()
				,$this->getProduct()->getTypeName()
			);
		}
		else if (!$this->hasCategory()) {
			$this->log(
				'Товару %s отказано в публикации,'
				.' потому что он не привязан ни к одному товарному разделу.'
				,$this->getProduct()->getTitle()
			);
		}
		else if (!$this->hasProperVisibility()) {
			$this->log(
				'Товару %s отказано в публикации,'
				.' потому что он виден на витрине только как часть другого товара,'
				.' и в то же время он не является простым вариантом настраиваемого товара.'
				,$this->getProduct()->getTitle()
			);
		}
		else if (!$this->hasPrice()) {
			$this->log(
				'Товару %s отказано в публикации, потому что для него отсутствует цена.'
				,$this->getProduct()->getTitle()
			);
		}
		else {
			$result = true;
		}
		return $result;
	}

	/**
	 * @override
	 * @return string|null
	 */
	protected function getCountryNameRussian() {
		if (!isset($this->{__METHOD__})) {
			/** @var string|null $result */
			$result = null;
			/** @var string|null $countryIso2Code */
			$countryIso2Code = $this->getProduct()->getCountryIso2Code();
			if ($countryIso2Code) {
				if (df_check_iso2($countryIso2Code)) {
					/** @var string $countryName */
					$result = df_h()->yandexMarket()->getCountryNameByIso2Code($countryIso2Code);
					if (!$result) {
						$this->notify(
							'Система сочла недопустимым значение «{значение}»'
							. ' в качестве страны товара {product}.'
							. ' Хотя это значение является корректным 2-буквенным кодом страны'
							. ' по стандарту ISO 3166-1 alpha-2,'
							. ' однако система не смогла сопоставить ему страну'
							. ' из справочника стран Яндекс.Маркета:'
							. ' http://partner.market.yandex.ru/pages/help/Countries.pdf'
							,array(
								'{значение}' => $countryIso2Code
								,'{product}' => $this->getProduct()->getTitle()
							)
						);
					}
				}
				else {
					/**
					 * В магазине sekretsna.com сюда вместо 2-сивольного кода страны попало значение «Турция»,
					 * потому что администраторы магазина
					 * переделали стандартное товарное свойство «country_of_manufacture»,
					 * заменив стандартный справочник стран на нестандартные текстовые названия стран.
					 * http://magento-forum.ru/index.php?app=members&module=messaging&section=view&do=showConversation&topicID=2105
					 */
					if (Df_YandexMarket_Config_Countries::s()->isNameValid($countryIso2Code)) {
						$result = $countryIso2Code;
					}
					else {
						$this->notify(
							'Система сочла недопустимым значение «{значение}»'
							. ' в качестве страны товара {product},'
							. ' потому что это значение не является ни двухсимвольным кодом страны'
							. ' по стандарту ISO, ни названием страны из справочника Яндекс.Маркета:'
							. ' http://partner.market.yandex.ru/pages/help/Countries.pdf'
							,array(
								'{значение}' => $countryIso2Code
								,'{product}' => $this->getProduct()->getTitle()
							)
						);
					}
				}
			}
			$this->{__METHOD__} = df_n_set($result);
		}
		return df_n_get($this->{__METHOD__});
	}

	/**
	 * 2015-10-28
	 * Опытным путём установил, что Яндекс.Маркет допускает длину адресов до 510 символов,
	 * а при превышении выдаёт сообщение «URL предложения не соответствует стандарту RFC-1738»:
	 * http://magento-forum.ru/topic/5282/
	 * При превышении нам надо отдавать Яндекс.Маркету адрес вида
	 * http://site.ru/catalog/product/view/id/1785
	 * @override
	 * @see Df_Catalog_Model_XmlExport_Product::getUrlMaxLength()
	 * @used-by Df_Catalog_Model_XmlExport_Product::getUrlForProduct()
	 * @return int
	 */
	protected function getUrlMaxLength() {return 510;}

	/** @return Df_YandexMarket_Settings */
	private function getSettings() {return Df_YandexMarket_Settings::s();}

	/** @return array(string => mixed) */
	private function getValue() {
		/** @var array(string => mixed) $result */
		$result = array(
			'url' => $this->getUrl()
			,'price' => $this->getPriceAsText()
			,'currencyId' => $this->getExportCurrency()->getCurrencyCode()
			,'categoryId' => $this->getCategoryId()
		);
		/**
		 * Обратите внимание, что тэг «market_category»
		 * должен следовать непосредственно после тэга «categoryId»,
		 * иначе валидатор Яндекс.Маркета сообщит о сбое:
		 * «Element «market_category» is not valid for content mode»
		 * http://magento-forum.ru/topic/3799/
		 * http://partner.market.yandex.ru/pages/help/shops.dtd
		 */
		if ($this->getYandexMarketCategoryName()) {
			$result['market_category'] = $this->getYandexMarketCategoryName();
		}
		if (!is_null($this->getImageUrl())) {
			$result['picture'] = $this->getImageUrl();
		}
		if ($this->getSettings()->general()->hasPointsOfSale()) {
			$result = array_merge($result, array(
				'store' => df_bts($this->getProduct()->isSalable())
				,'pickup' => df_bts($this->getSettings()->general()->isPickupAvailable())
				,'delivery' => df_bts(true)
			));
		}
		if (!$this->hasVendorInfo()) {
			$result['name'] = df_cdata($this->getProduct()->getName());
		}
		else {
			$result = array_merge($result, array(
				/**
				 * Раньше тут стояло
				 * $this->getProduct()->getAttributeText(Df_Catalog_Model_Product::P__MANUFACTURER)
				 * Df_Catalog_Helper_Product::getManufacturerNameByCode работет быстрее
				 */
				/**
				 * В магазине contactlinza.com.ua случился сбой
				 * при добавлении производителя «Johnson & Johnson»
				 * Warning: SimpleXMLElement::addChild()[simplexmlelement.addchild]:
				 * unterminated entity reference
				 */
				'vendor' => df_cdata($this->getProduct()->getManufacturerName())
				/**
				 * В магазине contactlinza.com.ua случился сбой
				 * при добавлении производителя «Johnson & Johnson»
				 * Warning: SimpleXMLElement::addChild()[simplexmlelement.addchild]:
				 * unterminated entity reference
				 */
				,'vendorCode' => df_cdata($this->getProduct()->getManufacturerCode())
				,'model' => df_cdata($this->getProduct()->getName())
			));
		}
		$result['description'] = df_cdata(df_nts($this->getProduct()->getDescription()));
		/** @var string|null $salesNotes */
		$salesNotes = $this->getProduct()->getData(Df_YandexMarket_Const::ATTRIBUTE__SALES_NOTES);
		if (!$salesNotes) {
			$salesNotes = df_cfg()->yandexMarket()->general()->getSalesNotes();
		}
		if ($salesNotes) {
			$result['sales_notes'] = df_cdata($salesNotes);
		}
		/**
		 * Яндекс.Маркет допускает не все названия стран.
		 * http://help.yandex.ru/partnermarket/?id=1111483
		 * http://partner.market.yandex.ru/pages/help/Countries.pdf
		 */
		if ($this->getCountryNameRussian()) {
			$result['country_of_origin'] = $this->getCountryNameRussian();
		}
		return $result;
	}

	/** @return bool */
	private function hasProperVisibility() {
		if (!isset($this->{__METHOD__})) {
			/** @var bool $result */
			$result =
					$this->getProduct()->isVisibleInSiteVisibility()
				||
					$this->getProduct()->isConfigurableChild()
			;
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return bool */
	private function hasVendorInfo() {return !!$this->getProduct()->getManufacturerCode();}
	
	/** @return string */
	private function getYandexMarketCategoryName() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $result */
			$result = $this->getProduct()->getData(Df_YandexMarket_Const::ATTRIBUTE__CATEGORY);
			if (!$result) {
				/** @var Df_Catalog_Model_Category|null $category */
				$category = $this->getCategory();
				while ($category) {
					$result = $category->getData(Df_YandexMarket_Const::ATTRIBUTE__CATEGORY);
					if ($result) {
						break;
					}
					if (!$category->getParentId()) {
						break;
					}
					// Работает в 10 раз быстрее, чем $category->getParentCategory()
					$category = $this->getDocument()->getCategories()->getItemById($category->getParentId());
				}
			}
			$this->{__METHOD__} = df_nts($result);
		}
		return $this->{__METHOD__};
	}

	/** @used-by Df_YandexMarket_Yml_Document::getProcessorClass_products() */
	
}