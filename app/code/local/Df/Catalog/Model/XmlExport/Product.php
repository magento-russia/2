<?php
/** @method Df_Catalog_Model_XmlExport_Catalog getDocument() */
abstract class Df_Catalog_Model_XmlExport_Product extends \Df\Xml\Generator\Part {
	/**
	 * Метод публичен, потому что его использует, например, метод
	 * @see Df_1C_Cml2_Export_Processor_Catalog_Attribute_Url::getЗначение()
	 * @return string
	 */
	public function getUrl() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->getUrlForProduct(
				$this->getConfigurableParent() ? $this->getConfigurableParent() : $this->getProduct()
			);
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Catalog_Model_Product */
	protected function getProduct() {
		// XDEBUG и WinCacheGrind говорят, что $this->cfg(self::P__PRODUCT) слишком медленно
		return $this->_getData(self::$P__PRODUCT);
	}

	/** @return array(int => Df_Catalog_Model_Category) */
	protected function getCategories() {
		if (!isset($this->{__METHOD__})) {
			/** @uses Df_Catalog_Model_Resource_Category_Collection::getCategories()  */
			$this->{__METHOD__} = array_map(
				array($this->getDocument()->getCategories(), 'getItemById'), $this->getCategoryIds()
			);
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Catalog_Model_Category|null */
	protected function getCategory() {
		if (!isset($this->{__METHOD__})) {
			// Должно работать быстрее, чем Df_Catalog_Model_Category::ld($this->getCategoryId());
			$this->{__METHOD__} = df_n_set(
				/**
				 * Видимо, разумнее использовать @uses is_null(), а не !,
				 * потому что корневой товарный раздел имеет идентификатор 0.
				 * И хотя в настоящее время корневой товарный раздел
				 * автоматически отбраковывается методом @see removeRootCategoryId(),
				 * однако вдруг я потом такую отбраковку уберу,
				 * а в данной точке код забуду поменять?
				 */
				is_null($this->getCategoryId())
				? null
				: $this->getDocument()->getCategories()->getItemById($this->getCategoryId())
			);
		}
		return df_n_get($this->{__METHOD__});
	}

	/**
	 * Используйте этот метод в тех случаях,
	 * когда внешняя информационная система (в частности, Яндекс.Маркет)
	 * разрешает привязывать товар только к одному товарному разделу.
	 * @return int|null
	 */
	protected function getCategoryId() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_n_set(df_first($this->getCategoryIds()));
		}
		return df_n_get($this->{__METHOD__});
	}

	/** @return int[] */
	protected function getCategoryIds() {
		if (!isset($this->{__METHOD__})) {
			/** @var int[] $result */
			$result = $this->removeRootCategoryId($this->getProduct()->getCategoryIds());
			// Если данный товар является вариантом составного товара,
			// и при этом данный товар не привязан ни к одному товарному разделу,
			// то назначаем этому товару товарный раздел первого из составных товаров-родителей.
			if (!$result && $this->getConfigurableParent()) {
				$result = $this->removeRootCategoryId($this->getConfigurableParent()->getCategoryIds());
			}
			df_result_array($result);
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Directory_Model_Country|null */
	protected function getCountry() {return $this->getProduct()->getCountry();}

	/** @return string|null */
	protected function getCountryNameRussian() {
		return $this->getCountry() ? $this->getCountry()->getNameRussian() : null;
	}

	/** @return string|null */
	protected function getImageUrl() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_n_set(
				!$this->getImage()
				? null
				: $this->preprocessUrl($this->getProduct()->getMediaConfig()->getMediaUrl(
					$this->getImage()
				))
			);
		}
		return df_n_get($this->{__METHOD__});
	}

	/** @return string[] */
	protected function getImageUrls() {
		if (!isset($this->{__METHOD__})) {
			/** @var string[] $urls */
			$urls = $this->getProduct()->getFull()->getImageUrls();
			if (!$urls && $this->getConfigurableParent()) {
				$urls = $this->getConfigurableParent()->getFull()->getImageUrls();
			}
			$this->{__METHOD__} = array_map(array($this, 'preprocessUrl'), $urls);
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	protected function getPriceAsText() {
		return rm_number_2f($this->convertMoneyToExportCurrency($this->getPriceInBaseCurrency()));
	}

	/** @return float */
	protected function getPriceInBaseCurrency() {
		return $this->getProduct()->getCompositeFinalPriceWithTax();
	}

	/**
	 * 2015-10-28
	 * @used-by getUrlForProduct()
	 * @see Df_YandexMarket_Model_Yml_Processor_Offer::getUrlMaxLength()
	 * http://stackoverflow.com/a/417184
	 * @return int
	 */
	protected function getUrlMaxLength() {return 2000;}

	/** @return bool */
	protected function hasCategory() {
		/**
		 * Видимо, разумнее использовать @uses is_null(), а не !,
		 * потому что корневой товарный раздел имеет идентификатор 0.
		 * И хотя в настоящее время корневой товарный раздел
		 * автоматически отбраковывается методом @see removeRootCategoryId(),
		 * однако вдруг я потом такую отбраковку уберу,
		 * а в данной точке код забуду поменять?
		 */
		return !is_null($this->getCategoryId());
	}

	/** @return bool */
	protected function hasPrice() {
		/**
		 * 2015-10-28
		 * Ошибочно сравнение (0 === $this->getPrice()),
		 * потому что $this->getPrice() всегда возвращает вещественное число.
		 * С другой стороны, проще и надёжнее написать !, чем (0.0 === $this->getPrice()).
		 */
		return !!$this->getPriceInBaseCurrency();
	}

	/**
	 * Этот метод используется в тех случаях,
	 * когда внешняя информационная система (в частности, Яндекс.Маркет)
	 * разрешает привязывать товар только к одному товарному разделу.
	 *
	 * Важный момент.
	 * Товар может быть привязан к нескольким товарным разделам.
	 * В том числе — к корневому, это я наблюдаю в магазине amilook.ru.
	 * Так вот, раньше тут стоял следующий программный код:
		$result = df_first($this->getProduct()->getCategoryIds());
	 * Если товар привязан к корневому товарному разделу,
	 * то в большинстве случаев приведённый выше код вернёт
	 * именно идентификатор корневого товарного раздела как наименьший.
	 * Однако в таком случае система будет не в состоянии построить для товара ЧПУ:
	 * http://magento-forum.ru/topic/3739/
	 * Так происходит потому, что система не добавляет в таблицу перенаправлений
	 * перенаправление для корневого раздела, да и практического смысла в этом нет.
	 * Поэтому надо скорректировать приведённый выше программный код таким образом,
	 * чтобы он не возвращал корневой раздел.
	 * @param int[] $categoryIds
	 * @return int|null
	 */
	private function chooseCategory(array $categoryIds) {
		return
			!$categoryIds
			? null
			: (
				(1 === count($categoryIds))
				? df_first($categoryIds)
				: df_first(array_diff(
					$categoryIds, array(rm_state()->getStoreProcessed()->getRootCategoryId())
				))
			)
		;
	}

	/** @return Df_Catalog_Model_Product|null */
	private function getConfigurableParent() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Catalog_Model_Product|null $result */
			$result = null;
			if ($this->getProduct()->isConfigurableChild()) {
				if (1 < count($this->getConfugurableParents())) {
					$this->notify(
						'Товар «{product}» входит в состав сразу нескольких товаров: {parents}.'
						."\nМодуль «{module}» при {context}"
						.' учтёт только один из родительских товаров и проигнорирует остальные.'
						,array(
							'{product}' => $this->getProduct()->getName()
							/** @uses Df_Catalog_Model_Product::getName() */
							,'{parents}' =>
								df_csv_pretty_quote(df_each($this->getConfugurableParents(), 'getName'))
							,'{module}' => $this->moduleTitle()
							,'{context}' => $this->getOperationNameInPrepositionalCase()
						)
					);
				}
				$result = df_first($this->getConfugurableParents());
			}
			$this->{__METHOD__} = df_n_set($result);
		}
		return df_n_get($this->{__METHOD__});
	}

	/** @return array(int => Df_Catalog_Model_Product) */
	private function getConfugurableParents() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				array_filter(
					array_map(
						/** @uses Df_Catalog_Model_Resource_Product_Collection::getItemById() */
						array($this->getDocument()->getProducts(), 'getItemById')
						, $this->getProduct()->getConfigurableParentIds()
					)
				)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return string|null */
	private function getImage() {
		if (!isset($this->{__METHOD__})) {
			/** @var string|null $result */
			$result = $this->getImageForProduct($this->getProduct());
			if ((!$result || ('no_selection' === $result)) && $this->getConfigurableParent()) {
				$result = $this->getImageForProduct($this->getConfigurableParent());
			}
			$this->{__METHOD__} = df_n_set(('no_selection' !== $result) ? $result : null);
		}
		return df_n_get($this->{__METHOD__});
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
		 * addAttributeToSelect('*') заружает не все свойства,
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

	/**
	 * 2015-10-28
	 * Перекрывается модулем Яндекс.Маркет:
	 * @see Df_YandexMarket_Model_Yml_Processor_Offer::getUrlForProduct()
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
			df_error('Товар {product} имеет недопустимый веб-адрес «{url}».', array(
				'{name}' => $product->getTitle(), '{url}' => $urlRaw
			));
		}
		$result = $this->preprocessUrl($urlRaw);
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
		if (!$forceShort && $this->getUrlMaxLength() < strlen(urlencode($urlRaw))) {
			$result = $this->getUrlForProduct($product, $forceShort = true);
		}
		return $result;
	}

	/**
	 * Важный момент.
	 * Товар может быть привязан к нескольким товарным разделам.
	 * В том числе — к корневому, это я наблюдаю в магазине amilook.ru.
	 * Так вот, раньше тут стоял следующий программный код:
	 * [code] $result = df_first($this->getProduct()->getCategoryIds()); [/code]
	 * Если товар привязан к корневому товарному разделу,
	 * то в большинстве случаев приведённый выше код вернёт
	 * именно идентификатор корневого товарного раздела как наименьший.
	 * Однако в таком случае система будет не в состоянии построить для товара ЧПУ:
	 * http://magento-forum.ru/topic/3739/
	 * Так происходит потому, что система не добавляет в таблицу перенаправлений
	 * перенаправление для корневого раздела, да и практического смысла в этом нет.
	 * Поэтому надо скорректировать приведённый выше программный код таким образом,
	 * чтобы он не возвращал корневой раздел.
	 * @param int[] $categoryIds
	 * @return int[]
	 */
	private function removeRootCategoryId(array $categoryIds) {
		return array_diff($categoryIds, array($this->store()->getRootCategoryId()));
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::$P__DOCUMENT, Df_Catalog_Model_XmlExport_Catalog::_C)
			->_prop(self::$P__PRODUCT, Df_Catalog_Model_Product::_C)
		;
	}
	const _C = __CLASS__;
	/** @var string */
	private static $P__PRODUCT = 'product';

	/**
	 * @used-by Df_Catalog_Model_XmlExport_Catalog::getProcessorForProduct()
	 * @param string $class
	 * @param Df_Catalog_Model_Product $product
	 * @param \Df\Xml\Generator\Document $document
	 * @return Df_Catalog_Model_XmlExport_Product
	 */
	public static function ic(
		$class
		,Df_Catalog_Model_Product $product
		,\Df\Xml\Generator\Document $document
	) {
		return df_ic($class, __CLASS__, array(
			self::$P__DOCUMENT => $document, self::$P__PRODUCT => $product
		));
	}
}