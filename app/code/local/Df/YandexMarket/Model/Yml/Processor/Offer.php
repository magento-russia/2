<?php
class Df_YandexMarket_Model_Yml_Processor_Offer extends Df_Core_Model_Abstract {
	/** @return array */
	public function getDocumentData() {
		/** @var array(string => mixed) $attributes */
		$attributes = array(
			'id' => $this->getProduct()->getId()
			,'available' => rm_bts($this->getProduct()->isInStock())
		);
		if ($this->hasVendorInfo()) {
			$attributes['type'] = 'vendor.model';
		}
		return array(
			Df_Varien_Simplexml_Element::KEY__ATTRIBUTES => $attributes
			,Df_Varien_Simplexml_Element::KEY__VALUE => $this->getValue()
		);
	}

	/** @return bool */
	public function isEnabled() {
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
			df_h()->yandexMarket()->log(
				'Товару «%s» отказано в публикации, потому что он типа «%s»,'
				.' а публикуются только товары типов «простой», «виртуальный», «комплект».'
				,$this->getProduct()->getName()
				,$this->getProduct()->getTypeName()
			);
		}
		else if (0 === $this->getCategoryId()) {
			df_h()->yandexMarket()->log(
				'Товару «%s» отказано в публикации,'
				.' потому что он не привязан ни к одному товарному разделу.'
				,$this->getProduct()->getName()
			);
		}
		else if (!$this->hasProperVisibility()) {
			df_h()->yandexMarket()->log(
				'Товару «%s» отказано в публикации,'
				.' потому что он виден на витрине только как часть другого товара,'
				.' и в то же время он не является простым вариантом настраиваемого товара.'
				,$this->getProduct()->getName()
			);
		}
		/**
		 * 2015-10-28
		 * Ошибочно сравнение (0 === $this->getPrice()),
		 * потому что $this->getPrice() всегда возвращает вещественное число.
		 * С другой стороны, проще и надёжнее написать !, чем (0.0 === $this->getPrice()).
		 */
		else if (!$this->getPrice()) {
			df_h()->yandexMarket()->log(
				'Товару «%s» отказано в публикации, потому что для него отсутствует цена.'
				,$this->getProduct()->getName()
			);
		}
		else {
			$result = true;
		}
		return $result;
	}

	/**
	 * @param int[] $categoryIds
	 * @return int
	 */
	private function chooseCategory(array $categoryIds) {
		/** @var int $result */
		/**
		 * Важный момент.
		 * Товар может быть привязан к нескольким товарным разделам.
		 * В том числе — к корневому, это я наблюдаю в магазине amilook.ru.
		 * Так вот, раньше тут стоял следующий программный код:
		 * [code]
				$result =
					rm_first(
						$this->getProduct()->getCategoryIds()
					)
				;
		 * [/code]
		 * Если товар привязан к корневому товарному разделу,
		 * то в большинстве случаев приведённый выше код вернёт
		 * именно идентификатор корневого товарного раздела как наименьший.
		 * Однако в таком случае система будет не в состоянии построить для товара ЧПУ:
		 * @link http://magento-forum.ru/topic/3739/
		 * Так происходит потому, что система не добавляет в таблицу перенаправлений
		 * перенаправление для корневого раздела, да и практического смысла в этом нет.
		 * Поэтому надо скорректировать приведённый выше программный код таким образом,
		 * чтобы он не возвращал корневой раздел.
		 */
		$result =
			rm_first(
				array_diff(
					$categoryIds
					,array(rm_state()->getStoreProcessed()->getRootCategoryId())
				)
			)
		;
		if (is_null($result)) {
			$result = rm_first($categoryIds);
		}
		df_result_integer($result);
		return $result;
	}
	
	/** @return Df_Catalog_Model_Category|null */
	private function getCategory() {
		if (!isset($this->{__METHOD__})) {
			// Должно работать быстрее, чем Df_Catalog_Model_Category::ld($this->getCategoryId());
			$this->{__METHOD__} = rm_n_set(
				!$this->getCategoryId()
				? null
				: $this->getDocument()->getCategories()->getItemById($this->getCategoryId())
			);
		}
		return rm_n_get($this->{__METHOD__});
	}
	
	/** @return int */
	private function getCategoryId() {
		if (!isset($this->{__METHOD__})) {
			/** @var int $result */
			$result = null;
			if (0 === count($this->getProduct()->getCategoryIds())) {
				/**
				 * Если данный товар является вариантом составного товара,
				 * и при этом данный товар не привязан ни к одному товарному разделу,
				 * то назначаем этому товару товарный раздел первого из составных товаров-родителей.
				 */
				if (!is_null($this->getConfigurableParent())) {
					if (0 < count($this->getConfigurableParent()->getCategoryIds())) {
						$result = $this->chooseCategory($this->getConfigurableParent()->getCategoryIds());
					}
				}
				$result = rm_nat0($result);
			}
			else {
				$result = $this->chooseCategory($this->getProduct()->getCategoryIds());
			}
			df_result_integer($result);
			if (0 === $result) {
				df_h()->yandexMarket()->notify(
					'Привяжите товар «%s» («%s») хотя бы к одному товарному разделу'
					,$this->getProduct()->getSku()
					,$this->getProduct()->getName()
				);
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Catalog_Model_Product|null */
	private function getConfigurableParent() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Catalog_Model_Product|null $result */
			$result = null;
			if ($this->getProduct()->isConfigurableChild()) {
				if (1 < count($this->getConfugurableParents())) {
					/** @var string[] $parentNames */
					$parentNames = array();
					/** @var string $parentNamesAsString */
					foreach ($this->getConfugurableParents() as $parent) {
						/** @var Df_Catalog_Model_Product $parent */
						$parentNames[]= $parent->getName();
					}
					df_h()->yandexMarket()->notify(
						'Товар «%s» входит в состав сразу нескольких товаров: %s.'
						."\r\nМодуль «Яндекс.Маркет» при формировании документа YML'
						.' учтёт только один из родительских товаров и проигнорирует остальные."
						,$this->getProduct()->getName()
						,df_quote_and_concat($parentNames)
					);
				}
				$result = rm_first($this->getConfugurableParents());
			}
			$this->{__METHOD__} = rm_n_set($result);
		}
		return rm_n_get($this->{__METHOD__});
	}

	/** @return Df_Catalog_Model_Product[] */
	private function getConfugurableParents() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Catalog_Model_Product[] $result */
			$result = array();
			if ($this->getProduct()->isConfigurableChild()) {
				foreach ($this->getProduct()->getConfigurableParentIds() as $parentId) {
					/** @var int $parentId */
					/** @var Df_Catalog_Model_Product|null $parent */
					$parent = $this->getDocument()->getProducts()->getItemById($parentId);
					if ($parent) {
						$result[]= $parent;
					}
				}
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_YandexMarket_Model_Yml_Document */
	private function getDocument() {return $this->cfg(self::P__DOCUMENT);}

	/** @return string|null */
	private function getImage() {
		if (!isset($this->{__METHOD__})) {
			/** @var string|null $result */
			$result = $this->getImageForProduct($this->getProduct());
			if (!$result && $this->getConfigurableParent()) {
				$result = $this->getImageForProduct($this->getConfigurableParent());
			}
			$this->{__METHOD__} = rm_n_set($result);
		}
		return rm_n_get($this->{__METHOD__});
	}

	/**
	 * @param Df_Catalog_Model_Product $product
	 * @return string|null
	 */
	private function getImageForProduct(Df_Catalog_Model_Product $product) {
		/** @var string|null $result */
		$result = $product->getData(Df_Catalog_Model_Product::P__IMAGE);
		/**
		 * Обратите внимание, что при включенном режиме денормализации таблицы товаров
		 * addAttributeToSelect ('*') заружает не все свойства,
		 * а только те, которые подлежат загрузке на странице товарного раздела.
		 *
		 * Например, при включенном режиме денормализации таблицы товаров
		 * в коллекцию не загружается свойство image,
		 * однако свойство small_image при этом загружается.
		 *
		 * Обратите внимание, что, начиная с версии 2.17.45,
		 * режим денормализации для данной коллекции товаров всегда отключен
		 * @see Df_YandexMarket_Model_Action_Front::getProducts()
		 * Однако вполне возможна ситуация,
		 * когда большой картинки у товара нет, а маленькая — есть.
		 */
		if (is_null($result)) {
			$result = $product->getData(Df_Catalog_Model_Product::P__SMALL_IMAGE);
		}
		if (!is_null($result)) {
			df_result_string($result);
		}
		return $result;
	}

	/** @return float */
	private function getPrice() {return $this->getProduct()->getCompositeFinalPriceWithTax();}

	/** @return string */
	private function getPriceAsText() {
		return df_h()->yandexMarket()->formatMoney(
			rm_currency()->convertFromBase(
				$this->getPrice(), $this->getSettings()->general()->getCurrencyCode()
			)
		);
	}

	/** @return Df_Catalog_Model_Product */
	private function getProduct() {
		// XDEBUG и WinCacheGrind говорят, что $this->cfg(self::P__PRODUCT) слишком медленно
		return $this->_getData(self::P__PRODUCT);
	}

	/** @return Df_YandexMarket_Model_Settings */
	private function getSettings() {return Df_YandexMarket_Model_Settings::s();}

	/** @return string */
	private function getUrl() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				$this->getUrlForProduct(
					$this->getConfigurableParent()
					? $this->getConfigurableParent()
					: $this->getProduct()
				)
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param Df_Catalog_Model_Product $product
	 * @param bool $forceShort [optional]
	 * @return string
	 */
	private function getUrlForProduct(Df_Catalog_Model_Product $product, $forceShort = false) {
		/** @var string $result */
		$product->setData(Df_Catalog_Model_Product::P__RM_CATEGORY_ID, $this->getCategoryId());
		/**
		 * 2015-10-28
		 * Короткий адрес формируем по аналогии с
		 * @see Mage_Catalog_Model_Product_Url::_getProductUrl()
		 * https://github.com/OpenMage/magento-mirror/blob/1.9.2.2/app/code/core/Mage/Catalog/Model/Product/Url.php#L278
		 * К сожалению, мы никак не может использовать этот метод,
		 * потому что он непубличен.
		 */
		/** @var string $urlRaw */
		$urlRaw =
			!$forceShort
			? $product->getProductUrl($useSid = false)
			: $product->getUrlModel()->getUrlInstance()->getUrl('catalog/product/view', array(
				'id' => $product->getId()
			));
		;
		/**
		 * Заметил, что в магазине sekretsna.com
		 * $product->getProductUrl($useSid = false)
		 * возвращает значения вроде «http://sekretsna.com//la-scala-bpr-12-semejnoe-160x220x2.html»,
		 * то есть, с лишним  символом «/» после имени домена,
		 * что оставалось незамеченным в данной точке программы и приводило к сбою в дальнейшем.
		 */
		if (!Df_Zf_Validate_Uri::s()->isValid($urlRaw)) {
			df_error(strtr('Товар «{name}» («{sku}») имеет недопустимый веб-адрес «{url}».', array(
				'{name}' => $product->getName()
				,'{sku}' => $product->getSku()
				,'{url}' => $urlRaw
			)));
		}
		$result = df_h()->yandexMarket()->preprocessUrl($urlRaw);
		df_result_string_not_empty($result);
		$product->unsetData(Df_Catalog_Model_Product::P__RM_CATEGORY_ID);
		/**
		 * 2015-10-28
		 * Опытным путём установил, что Яндекс.Маркет допускает длину адресов до 510 символов,
		 * а при превышении выдаёт сообщение «URL предложения не соответствует стандарту RFC-1738»:
		 * http://magento-forum.ru/topic/5282/
		 * При превышении нам надо отдавать Яндекс.Маркету адрес вида
		 * http://site.ru/catalog/product/view/id/1785
		 */
		if (!$forceShort && 510 < strlen(urlencode($urlRaw))) {
			$result = $this->getUrlForProduct($product, $forceShort = true);
		}
		return $result;
	}

	/** @return array(string => mixed) */
	private function getValue() {
		/** @var array(string => mixed)  $result */
		$result = array(
			'url' => $this->getUrl()
			,'price' => $this->getPriceAsText()
			,'currencyId' => $this->getSettings()->general()->getCurrencyCode()
			,'categoryId' => $this->getCategoryId()
		);
		/**
		 * Обратите внимание, что тэг «market_category»
		 * должен следовать непосредственно после тэга «categoryId»,
		 * иначе валидатор Яндекс.Маркета сообщит о сбое:
		 * «Element «market_category» is not valid for content mode»
		 * @link http://magento-forum.ru/topic/3799/
		 * @link http://partner.market.yandex.ru/pages/help/shops.dtd
		 */
		if ($this->getYandexMarketCategoryName()) {
			$result['market_category'] = $this->getYandexMarketCategoryName();
		}
		if (!is_null($this->getImage())) {
			$result['picture'] =
				df_h()->yandexMarket()->preprocessUrl(
					$this->getProduct()->getMediaConfig()->getMediaUrl(
						$this->getImage()
					)
				)
			;
		}
		if ($this->getSettings()->general()->hasPointsOfSale()) {
			$result = array_merge($result, array(
				'store' => rm_bts($this->getProduct()->isSalable())
				,'pickup' => rm_bts($this->getSettings()->general()->isPickupAvailable())
				,'delivery' => rm_bts(true)
			));
		}
		if (!$this->hasVendorInfo()) {
			$result['name'] = rm_cdata($this->getProduct()->getName());
		}
		else {
			$result = array_merge($result, array(
				'vendor' =>
					/**
					 * Раньше тут стояло
					 *
						$this->getProduct()->getAttributeText(
							Df_Catalog_Model_Product::P__MANUFACTURER
						)
					 *
					 * Df_Catalog_Helper_Product::getManufacturerNameByCode работет быстрее
					 */
					/**
					 * В магазине contactlinza.com.ua случился сбой
					 * при добавлении производителя «Johnson & Johnson»
					 * Warning: SimpleXMLElement::addChild()[simplexmlelement.addchild]:
					 * unterminated entity reference
					 */
					rm_cdata(df_h()->catalog()->product()->getManufacturerNameByCode(
						$this->getProduct()->getData(Df_Catalog_Model_Product::P__MANUFACTURER)
					))
				,'vendorCode' =>
					/**
					 * В магазине contactlinza.com.ua случился сбой
					 * при добавлении производителя «Johnson & Johnson»
					 * Warning: SimpleXMLElement::addChild()[simplexmlelement.addchild]:
					 * unterminated entity reference
					 */
					rm_cdata($this->getProduct()->getData(Df_Catalog_Model_Product::P__MANUFACTURER))
				,'model' => rm_cdata($this->getProduct()->getName())
			));
		}
		$result['description'] = rm_cdata(df_nts($this->getProduct()->getDescription()));
		/** @var string|null $salesNotes */
		$salesNotes = $this->getProduct()->getData(Df_YandexMarket_Const::ATTRIBUTE__SALES_NOTES);
		if (!$salesNotes) {
			$salesNotes = df_cfg()->yandexMarket()->general()->getSalesNotes();
		}
		if ($salesNotes) {
			$result['sales_notes'] = rm_cdata($salesNotes);
		}
		/** @var string|null $countryIso2Code */
		$countryIso2Code = $this->getProduct()->getData(Df_Catalog_Model_Product::P__COUNTRY_OF_MANUFACTURE);
		if ($countryIso2Code) {
			/** @var string $countryName */
			$countryName = null;
			if (2 === strlen($countryIso2Code)) {
				/** @var string $countryName */
				$countryName = df_h()->yandexMarket()->getCountryNameByIso2Code($countryIso2Code);
				if (!$countryName) {
					df_h()->yandexMarket()->notify(strtr(
						'Система сочла недопустимым значение «{значение}» в качестве страны товара «{name}» («{sku}»).'
						.' Хотя это значение является корректным двухсимвольным кодом страны по стандарту ISO,'
						. ' однако система не смогла сопоставить ему страну из справочника стран Яндекс.Маркета:'
						. ' http://partner.market.yandex.ru/pages/help/Countries.pdf'
						,array(
							'{значение}' => $countryIso2Code
							,'{name}' => $this->getProduct()->getName()
							,'{sku}' => $this->getProduct()->getSku()
						)
					));
				}
			} else {
				/**
				 * В магазине sekretsna.com сюда вместо 2-сивольного кода страны попало значение «Турция»,
				 * потому что администраторы магазина переделали стандартное товарное свойство «country_of_manufacture»,
				 * заменив стандартный справочник стран на нестандартные текстовые названия стран.
				 * @link http://magento-forum.ru/index.php?app=members&module=messaging&section=view&do=showConversation&topicID=2105
				 */
				if (Df_YandexMarket_Model_Config_Countries::s()->isNameValid($countryIso2Code)) {
					$countryName = $countryIso2Code;
				}
				else {
					df_h()->yandexMarket()->notify(strtr(
						'Система сочла недопустимым значение «{значение}» в качестве страны товара «{name}» («{sku}»),'
						.' потому что это значение не является ни двухсимвольным кодом страны по стандарту ISO,'
						. ' ни названием страны из справочника Яндекс.Маркета:'
						. ' http://partner.market.yandex.ru/pages/help/Countries.pdf'
						,array(
							'{значение}' => $countryIso2Code
							,'{name}' => $this->getProduct()->getName()
							,'{sku}' => $this->getProduct()->getSku()
						)
					));
				}
			}
			/**
			 * Яндекс.Маркет допускает не все названия стран.
			 * @link http://help.yandex.ru/partnermarket/?id=1111483
			 * @link http://partner.market.yandex.ru/pages/help/Countries.pdf
			 */
			if ($countryName) {
				$result['country_of_origin'] = $countryName;
			}
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
	private function hasVendorInfo() {
		return !is_null($this->getProduct()->getData(Df_Catalog_Model_Product::P__MANUFACTURER));
	}
	
	/** @return string */
	private function getYandexMarketCategoryName() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $result */
			$result = $this->getProduct()->getData(Df_YandexMarket_Const::ATTRIBUTE__CATEGORY);
			if (!$result) {
				/** @var Mage_Catalog_Model_Category|null $category */
				$category = $this->getCategory();
				while (!is_null($category)) {
					$result = $category->getData(Df_YandexMarket_Const::ATTRIBUTE__CATEGORY);
					if ($result) {
						break;
					}
					if (!$category->getParentId()) {
						break;
					}
					$category =
						// Работает в 10 раз быстрее, чем $category->getParentCategory()
						$this->getDocument()->getCategories()->getItemById(
							$category->getParentId()
						)
					;
				}
			}
			$this->{__METHOD__} = df_nts($result);
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__DOCUMENT, Df_YandexMarket_Model_Yml_Document::_CLASS)
			->_prop(self::P__PRODUCT, 'Mage_Catalog_Model_Product')
		;
	}
	const _CLASS = __CLASS__;
	const P__DOCUMENT = 'document';
	const P__PRODUCT = 'product';
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_YandexMarket_Model_Yml_Processor_Offer
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}

}