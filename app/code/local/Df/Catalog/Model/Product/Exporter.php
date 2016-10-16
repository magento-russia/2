<?php
class Df_Catalog_Model_Product_Exporter extends Df_Core_Model {
	/** @return Df_Catalog_Model_Resource_Product_Collection */
	public function getResult() {
		if (!isset($this->{__METHOD__})) {
			/**
			 * Отключение денормализации позволяет иметь в коллекции товаров все необходимые нам свойства.
			 * Вместо отключения денормализации есть и другой способ иметь все необходиые свойства:
			 * указать в установочном скрипте,
			 * что требуемые свойства должны попадать в коллекцию в режиме денормализации.
			 * @see Df_Shipping_Setup_2_16_2::process()
			 * Однако значения поля «описание» могут быть очень длинными,
			 * и если добавить колонку для этого свойства в денормализованную таблицу товаров,
			 * то тогда мы можем превысить устанавливаемый MySQL предел для одной строки таблицы
			 *
			 * «Magento по умолчанию отводит на хранение значения одного свойства товара
			 * в своей базе данных 255 символов, для хранения которых MySQL выделяет 255 * 3 + 2 = 767 байтов.
			 * Magento объединяет все свойства товаров в единой расчётной таблице,
			 * колонками которой служат свойства, а строками — товары.
			 * Если свойств товаров слишком много,
			 * то Magento превышает системное ограничение MySQL на одну строку таблицы:
			 * 65535 байтов,что приводит к сбою построения расчётной таблицы товаров»
			 *
			 * Либо же значение поля описание будет обрезаться в соответствии с установленным администратором
			 * значением опции «Российская сборка» → «Административная часть» → «Расчётные таблицы» →
			 * «Максимальное количество символов для хранения значения свойства товара».
			 */
			/** @var Df_Catalog_Model_Resource_Product_Collection $result */
			$result = Df_Catalog_Model_Product::c($disableFlat = true);
			/**
			 * Обратите внимание, что при включенном режиме денормализации таблицы товаров
			 * addAttributeToSelect('*') заружает не все свойства,
			 * а только те, которые подлежат загрузке на странице товарного раздела.
			 * Например, при включенном режиме денормализации таблицы товаров
			 * в коллекцию не загружаются свойства image и description.
			 */
			/**
			 * Раньше тут стояло $result->addAttributeToSelect('*');
			 * Конкретное перечисление загружаемых товарных свойств
			 * позволяет ускорить работу модуля и снизить требования к ресурсам PHP и MySQL
			 */
			$result->addAttributeToSelect($this->getAttributesToSelect());
			/**
			 * Раньше тут стоял код отбраковки товаров с нулевой (неуказанной) ценой:
				$result->addFieldToFilter(array(array(
					'attribute' =>Df_Catalog_Model_Product::P__PRICE
					,'gt' => 0
				)));
			 * Однако отбраковка товаров с нулевой ценой, помимо всего прочего,
			 * отсеивает товарные комплекты, что неверно:
			 * http://magento-forum.ru/topic/3780/
			 *
			 * Вместо этого система теперь отфильтровывает простые товары с нулевой ценой
			 * в методе @see Df_YandexMarket_Model_Yml_Processor_Offer::isEnabled
			 *
			 * Для товаров типов CONFIGURABLE, GROUPED, BUNDLE
			 * стандартный метод получения цены (getPrice) в Magento всегда возвращает 0.
			 *
			 * Товары типов CONFIGURABLE и GROUPED отсеиваются методом
			 * @see Df_YandexMarket_Model_Yml_Processor_Offer::isEnabled()
			 *
			 * Для товаров типа BUNDLE в качестве цены берётся
			 * наименьшая возможная цена приобретения комплекта.
			 * @see Df_YandexMarket_Model_Yml_Processor_Offer::getPrice()
			 * http://magento-forum.ru/topic/3800/
			 */
			$result->addStoreFilter(df_state()->getStoreProcessed());
			$this->{__METHOD__} = $result;
			$this->applyRule();
			/**
			 * Не надо добавлять $result->addUrlRewrite(),
			 * иначе адреса товарных страниц не будут содержать товарные разделы.
			 * @see Mage_Catalog_Model_Product_Url::getUrl
			 * [code]
					$requestPath = $product->getRequestPath();
  			 		if (empty($requestPath) && $requestPath !== false) {
			 * [/code]
			 */
			/**
			 * 2015-10-28
			 * Метод @see Mage_Catalog_Model_Resource_Product_Collection::addUrlRewrite()
			 * устроен таким образом, что если вызвать его без параметров,
			 * то адреса товаров не будут содержать товарный раздел:
			 * https://github.com/OpenMage/magento-mirror/blob/1.9.2.2/app/code/core/Mage/Catalog/Model/Resource/Product/Collection.php#L1143-L1147
			 * Если же этот метод не вызывать и ничего дальше дополнительно не предпринимать,
			 * то наоборот адреса страниц ВСЕГДА будут содержать товарный раздел:
			 *
				if (Mage::getStoreConfig(
					Mage_Catalog_Helper_Product::XML_PATH_PRODUCT_URL_USE_CATEGORY
			 		, $this->getStoreId()
			 	)) {
					$this->_urlRewriteCategory = $categoryId;
				} else {
					$this->_urlRewriteCategory = 0;
				}
			 *
			 * Метод так реализован, видимо, потому что, на витрине его принято вызывать с параметром
			 * в виде текущего товарного раздела (того раздела, где находится посетитель).
			 * В нашем же случае текущего раздела нет, и вызов этого метода без параметров
			 * работает не так, как нам нужно.
			 * Поэтому вызываем его только если нам не нужно добавлять разделы в адрес товара.
			 */
			if (!Mage::getStoreConfig(
				Mage_Catalog_Helper_Product::XML_PATH_PRODUCT_URL_USE_CATEGORY
				, df_state()->getStoreProcessed()
			)) {
				$result->addUrlRewrite();
			}
			/**
			 * Старый комментарий:
			 * Cистема оставляла в коллекции только те товары,
			 * для которых администратор указал,
			 * что они должны быть видны в списке товаров на витрине.
			 * При этом $result->setVisibility($productVisibility->getVisibleInSiteIds());
			 * почему-то не работает в многовитринном магазине soundmaster.ua
			 *
			 * addAttributeToFilter хоть и выглядит как устаревший способ фильтрации по видимости,
			 * по результатам тестирования работает корректно
			 * (тестировал на soundmaster.ua и leto-krasnodar.ru).
			 *
			 * 2014-04-12
			 * Раньше тут выполнялась отбраковка не подлежащих публикации товаров:
				$result->addAttributeToFilter(
					'visibility'
					,array('in' => df_mage()->catalog()->product()->visibility()->getVisibleInSiteIds())
				);
			 * Теперь я от такой отбраковки отказался,
			 * потому что она отбраковывает простые варианты настраиваемых товаров
			 * (у них практически значением видимости является «Виден только как часть другого товара»).
			 * В то же время, настраиваемые товары-родители отбраковываются в методе
			 * @see Df_YandexMarket_Model_Yml_Processor_Offer::isEnabled()
			 * Таким образом, получалось,
			 * что настраиваемые товары вообще публиковались на Яндекс.Маркете.
			 * Теперь я сделал по-другому:
			 * отбраковку по видимости я здесь не произожу,
			 * а вместо этого доработал метод @see Df_YandexMarket_Model_Yml_Processor_Offer::isEnabled(),
			 * чтобы там отбраковывать только не невидимые товары,
			 * которые не являются составными частями подлежащих публикации настраиваемых товаров
			 * (в то же время настраиваемые товары-родители по-прежнему на Яндекс.Маркете не публикуются,
			 * и, видимо — это правильно).
			 *
			 * Обратите внимание, что при публикации на Яндекс.Маркете
			 * таких простых вариантов настраиваемых товаров,
			 * которые «Видны только как части других товаров»,
			 * в качестве веб-адреса таких товаров
			 * надо указывать веб-адрес настраиваемого товара-родителя,
			 * потому что система не будет отображать витринные страницы
			 * простых вариантов настраиваемых товаров которые «Видны только как части других товаров».
			 */
			/**
			 * Заставляем систему загружать в коллекцию значение видимости,
			 * чтобы использовать его в методе @see Df_YandexMarket_Model_Yml_Processor_Offer::isEnabled()
			 */
			$result->addAttributeToSelect('visibility');
			if ($this->needRemoveNotSalable()) {
				/**
				 * Удаляем из коллекции те товары,
				 * которые администратор пометил как неподлежащие продаже
				 */
				$result->addAttributeToFilter('status', array(
					'in' => df_mage()->catalog()->product()->statusSingleton()->getSaleableStatusIds()
				));
			}
			if ($this->needRemoveOutOfStock()) {
				/**
				 * Удаляем из коллекции отсутствующие на складе товары.
				 * Обратите внимание, что такая проверка не учитывает положительность складского остатка,
				 * а всего лишь учитывает значение опции «В наличии ли данный товар?».
				 * Однако значением опции «В наличии ли данный товар?» может быть «в наличии»
				 * даже при нулевом складском остатке!
				 */
				df_mage()->catalogInventory()->stockSingleton()->addInStockFilterToCollection($result);
			}
			/**
			 * Раньше тут был код:
				if (0 < df_cfg()->yandexMarket()->products()->getMinQty()) {
					$result
						->joinField(
							'qty',
							'cataloginventory/stock_item',
							'qty',
							'product_id=entity_id',
							'{{table}}.stock_id=' . Mage_CatalogInventory_Model_Stock::DEFAULT_STOCK_ID,
							'left'
						)
						->addAttributeToFilter(
							'qty'
							,array('ge' => df_cfg()->yandexMarket()->products()->getMinQty())
						)
					;
				}
			 */
			if ($this->limit()) {
				$result->getSelect()->limit($this->limit());
			}
			/**
			 * Обратите внимание, что метод addCategoryIds
			 * работает только после загрузки коллекции.
			 * Товарные разделы нужны нам
			 * в методе Df_YandexMarket_Model_Yml_Processor_Offer::getCategoryId.
			 *
			 * Метод Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection::addCategoryIds
			 * отсутствует в Magento CE 1.4.0.1
			 */
			//$result->printLogQuery(false, true);
			$result->load();
			$result->addCategoryIds();
		}
		return $this->{__METHOD__};
	}

	/**
	 * @used-by Df_Catalog_Product_Exporter::getAttributesToSelect()
	 * @return string|string[]
	 */
	protected function additionalAttributes() {
		return $this->cfg(self::P__ADDITIONAL_ATTRIBUTES, array());
	}

	/** @return string|string[] */
	protected function getAttributesToSelect() {
		return
			$this->needLoadAllAttributes()
			? '*'
			: array_merge($this->additionalAttributes(), array(
				Df_Catalog_Model_Product::P__COUNTRY_OF_MANUFACTURE
				,Df_Catalog_Model_Product::P__DESCRIPTION
				,Df_Catalog_Model_Product::P__IMAGE
				,Df_Catalog_Model_Product::P__IS_SALABLE
				,Df_Catalog_Model_Product::P__MANUFACTURER
				,Df_Catalog_Model_Product::P__NAME
				,Df_Catalog_Model_Product::P__PRICE
				,Df_Catalog_Model_Product::P__SKU
				,Df_Catalog_Model_Product::P__SMALL_IMAGE
				,Df_Catalog_Model_Product::P__STORE_ID
				,Df_Catalog_Model_Product::P__URL_KEY
				,Df_YandexMarket_Const::ATTRIBUTE__CATEGORY
				,Df_YandexMarket_Const::ATTRIBUTE__SALES_NOTES
			));
	}

	/**
	 * @used-by Df_Catalog_Product_Exporter::getResult()
	 * @return int
	 */
	protected function limit() {return $this->cfg(self::P__LIMIT, 0);}

	/**
	 * @used-by Df_Catalog_Product_Exporter::getResult()
	 * @return bool
	 */
	protected function needRemoveNotSalable() {return $this->cfg(self::P__NEED_REMOVE_NOT_SALABLE, false);}

	/**
	 * @used-by Df_Catalog_Product_Exporter::getResult()
	 * @return bool
	 */
	protected function needRemoveOutOfStock() {return $this->cfg(self::P__NEED_REMOVE_OUT_OF_STOCK, false);}

	/**
	 * 2016-10-09
	 * @used-by Df_Catalog_Product_Exporter::applyRule()
	 * @return void
	 */
	protected function noMatchingProductIds() {}

	/**
	 * @used-by Df_Catalog_Product_Exporter::applyRule()
	 * @return Mage_CatalogRule_Model_Rule|null
	 */
	protected function rule() {return $this->cfg(self::P__RULE);}

	/** @return void */
	private function applyRule() {
		/** @var Mage_CatalogRule_Model_Rule|null $rule */
		$rule = $this->rule();
		if ($rule) {
			/**
			 * Фильтрация на основе правил — очень ресурсоёмкая операция.
			 * Если правила не заданы — то избегаем этой фильтрации.
			 * @var bool $needApplyRule
			 */
			$needApplyRule = true;
			if ($rule->getConditions() instanceof Mage_Rule_Model_Condition_Combine) {
				/** @var Mage_Rule_Model_Condition_Combine $combinedConditions */
				$combinedConditions = $rule->getConditions();
				$needApplyRule =
					is_array($combinedConditions->getConditions())
					&& $combinedConditions->getConditions()
				;
			}
			if ($needApplyRule) {
				/** @var int[]|array(int => array(int => int)) $matchingProductIdsRaw */
				$matchingProductIdsRaw = $rule->getMatchingProductIds();
				/**
				 * В Magento CE, начиная с версии 1.8.0.0,
				 * метод @see Mage_CatalogRule_Model_Rule::getMatchingProductIds()
				 * возвращает даныне в таком формате:
					Array
					(
						[10703] => Array
							(
								[1] => 1
								[0] => 1
							)

						[10704] => Array
							(
								[1] => 1
								[0] => 1
							)
					 )
				 * Здесь 10703, 10704 — идентификаторы товаров,
				 * ключи массива для каждого товара — идентификаторы магазинов,
				 * а значения ключей массива для каждого товара — флаги принадлежности товара
				 * заданному правилом множеству товаров.
				 *
				 * В Magento CE более ранних версий (например, 1.7.0.2)
				 * метод @see Mage_CatalogRule_Model_Rule::getMatchingProductIds()
				 * возвращает просто массив идентификаторов
				 * принадлежащих заданному привилом множеству товаров.
				 * http://magento-forum.ru/topic/4239/
				 * http://magento-forum.ru/topic/4340/
				 */
				if ($matchingProductIdsRaw) {
					/** @var int[] $matchingProductIds */
					/** @var int|array(int => int) $matchingProductIdRawTest */
					$matchingProductIdRawTest = df_first($matchingProductIdsRaw);
					if (!is_array($matchingProductIdRawTest)) {
						$matchingProductIds = df_int_simple(array_values($matchingProductIdsRaw));
					}
					else {
						$matchingProductIds = array();
						/**
						 * 2015-11-07
						 * Тут раньше стояло
						 * $storeId = df_nat0(df_state()->getStoreProcessed()->getId());
						 * и дальше шла выборка идентификатору магазина.
						 * Это ошибочно и очевидно являлось недоразумением,
						 * потому что даже в Magento CE 1.8.0.0
						 * ключами элементов массива $matchingProductIdsRaw являются идентификаторы сайтов,
						 * а не магазинов:
						 * https://github.com/OpenMage/magento-mirror/blob/94e611e127d5c14008990b256ed06ded622dcee9/app/code/core/Mage/CatalogRule/Model/Rule.php#L268
						 * @see Mage_CatalogRule_Model_Rule::callbackValidateProduct()
							foreach ($this->_getWebsitesMap() as $websiteId => $defaultStoreId) {
								$product->setStoreId($defaultStoreId);
							$results[$websiteId] = (int)$this->getConditions()->validate($product);
							}
						 */
						/** @var int $websiteId */
						$websiteId = df_nat0(df_state()->getStoreProcessed()->getWebsiteId());
						/**
							[10704] => Array
								(
									[1] => 1
									[0] => 1
								)
						 */
						foreach ($matchingProductIdsRaw as $matchingProductId => $matchingProductIdRaw) {
							/** @var int $matchingProductId */
							/** @var array(int => int) $matchingProductIdRaw */
							if ($matchingProductIdRaw && dfa($matchingProductIdRaw, $websiteId)) {
								$matchingProductIds[]= (int)$matchingProductId;
							}
						}
					}
					/**
					 * 2015-11-07
					 * Если массив $matchingProductIds пуст, то метод
					 * @uses Mage_Catalog_Model_Resource_Product_Collection::addIdFilter()
					 * сразу помечает коллекцию как загруженную:
					 * https://github.com/OpenMage/magento-mirror/blob/053e0b286cbd6d52ac69ca9fd53a3b72c78aca1d/app/code/core/Mage/Catalog/Model/Resource/Product/Collection.php#L599-L602
					 * Такое состояние явно свидетельствует об ошибке администратора,
					 * поэтому лучше сразу сообщить ему об этом конкретно, а не обобщённо.
					 */
					$this->noMatchingProductIds();
					// Вместо addIdFilter можно ещё использовать метод addIdFilterClientSide —
					// он позволяет уменьшить текст запроса SQL.
					$this->getResult()->addIdFilter($matchingProductIds);
				}
			}
		}
	}

	/** @return bool */
	private function needLoadAllAttributes() {return $this->cfg(self::P__NEED_LOAD_ALL_ATTRIBUTES, false);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__ADDITIONAL_ATTRIBUTES, DF_V_ARRAY, false)
			->_prop(self::P__LIMIT, DF_V_INT, false)
			->_prop(self::P__NEED_LOAD_ALL_ATTRIBUTES, DF_V_BOOL, false)
			->_prop(self::P__NEED_REMOVE_NOT_SALABLE, DF_V_BOOL, false)
			->_prop(self::P__NEED_REMOVE_OUT_OF_STOCK, DF_V_BOOL, false)
			->_prop(self::P__RULE, 'Mage_CatalogRule_Model_Rule', false)
		;
	}

	const P__ADDITIONAL_ATTRIBUTES = 'additional_attributes';
	const P__LIMIT = 'limit';
	const P__NEED_LOAD_ALL_ATTRIBUTES = 'need_load_all_attributes';
	const P__NEED_REMOVE_NOT_SALABLE = 'need_remove_not_salable';
	const P__NEED_REMOVE_OUT_OF_STOCK = 'need_remove_out_of_stock';
	const P__RULE = 'rule';
	/**
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Catalog_Model_Product_Exporter
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}