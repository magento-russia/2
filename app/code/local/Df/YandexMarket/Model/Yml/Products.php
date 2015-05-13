<?php
class Df_YandexMarket_Model_Yml_Products extends Df_Core_Model_Abstract {
	/** @return Df_Catalog_Model_Resource_Product_Collection */
	public function getProducts() {
		if (!isset($this->{__METHOD__})) {
			/** @var  Df_Catalog_Model_Resource_Product_Collection $result */
			$result =
				/**
				 * Отключение денормализации позволяет иметь в коллекции товаров все необходимые нам свойства.
				 * Вместо отключения денормализации есть и другой способ иметь все необходиые свойства:
				 * указать в установочном скрипте,
				 * что требуемые свойства должны попадать в коллекцию в режиме денормализации.
				 * @see Df_Shipping_Model_Setup_2_16_2::process()
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
				Df_Catalog_Model_Resource_Product_Collection::i(
					array(
						Df_Catalog_Model_Resource_Product_Collection::P__DISABLE_FLAT => true
					)
				)
			;
			$this->{__METHOD__} = $result;
			$this->addAttributes();
			/**
			 * Раньше тут стоял код отбраковки товаров с нулевой (неуказанной) ценой:
				$result
					->addFieldToFilter(
						array(
							array(
								'attribute' =>Df_Catalog_Model_Product::P__PRICE
								,'gt' => 0
							)
						)
					)
				;
			 * Однако отбраковка товаров с нулевой ценой, помимо всего прочего,
			 * отсеивает товарные комплекты, что неверно:
			 * @link http://magento-forum.ru/topic/3780/
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
			 * @link http://magento-forum.ru/topic/3800/
			 */
			$result->addStoreFilter(rm_state()->getStoreProcessed());
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
			/**
			 * Удаляем из коллекции те товары,
			 * которые администратор пометил как неподлежащие продаже
			 */
			$result->addAttributeToFilter('status', array(
				'in' => df_mage()->catalog()->product()->statusSingleton()->getSaleableStatusIds()
			));
			/**
			 * Удаляем из коллекции отсутствующие на складе товары
			 */
			if (!df_cfg()->yandexMarket()->products()->needPublishOutOfStock()) {
				/**
				 * Удаляем из коллекции отсутствующие на складе товары.
				 * Обратите внимание, что такая проверка не учитывает положительность складского остатка,
				 * а всего лишь учитывает значение опции «В наличии ли данный товар?».
				 * Однако значением опции «В наличии ли данный товар?» может быть «в наличии»
				 * даже при нулевом складском остатке!
				 */
				df_mage()->catalogInventory()->stockSingleton()
					->addInStockFilterToCollection($result)
				;
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
			if (
					df_cfg()->yandexMarket()->diagnostics()->isEnabled()
				&&
					df_cfg()->yandexMarket()->diagnostics()->needLimit()
			) {
				$result->getSelect()->limit(
					df_cfg()->yandexMarket()->diagnostics()->getLimit()
				);
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
			if (method_exists($result, 'addCategoryIds')) {
				call_user_func(array($result, 'addCategoryIds'));
			}
			else {
				$result->addCategoryIdsRm();
			}
		}
		return $this->{__METHOD__};
	}

	/** @return void */
	private function addAttributes() {
		/**
		 * Обратите внимание, что при включенном режиме денормализации таблицы товаров
		 * addAttributeToSelect ('*') заружает не все свойства,
		 * а только те, которые подлежат загрузке на странице товарного раздела.
		 * Например, при включенном режиме денормализации таблицы товаров
		 * в коллекцию не загружаются свойства image и description.
		 */
		/**
		 * Раньше тут стояло $result->addAttributeToSelect('*');
		 * Конкретное перечисление загружаемых товарных свойств
		 * позволяет ускорить работу модуля и снизить требования к ресурсам PHP и MySQL
		 */
		$this->getProducts()->addAttributeToSelect(array(
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

	/** @return void */
	private function applyRule() {
		/** @var Mage_CatalogRule_Model_Rule|null $rule */
		$rule = df_cfg()->yandexMarket()->products()->getRule();
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
					&&
						(0 < count($combinedConditions->getConditions()))
				;
			}
			if ($needApplyRule) {
				/** @var int[]|array(int => array(int => int)) $matchingProductIdsRaw */
				$matchingProductIdsRaw =
					df_cfg()->yandexMarket()->products()->getRule()->getMatchingProductIds()
				;
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
				 * @link http://magento-forum.ru/topic/4239/
				 * @link http://magento-forum.ru/topic/4340/
				 */
				if ($matchingProductIdsRaw) {
					/** @var int[] $matchingProductIds */
					/** @var int|array(int => int) $matchingProductIdRawTest */
					$matchingProductIdRawTest = rm_first($matchingProductIdsRaw);
					if (!is_array($matchingProductIdRawTest)) {
						$matchingProductIds = rm_int(array_values($matchingProductIdsRaw));
					}
					else {
						$matchingProductIds = array();
						/** @var int $storeId */
						$storeId = rm_nat0(rm_state()->getStoreProcessed()->getId());
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
							if ($matchingProductIdRaw && df_a($matchingProductIdRaw, $storeId)) {
								$matchingProductIds[]= rm_nat($matchingProductId);
							}
						}
					}
					/**
					 * Вместо addIdFilter можно ещё использовать метод addIdFilterClientSide —
					 * он позволяет уменьшить текст запроса SQL
					 */
					$this->getProducts()->addIdFilter($matchingProductIds);
				}
			}
		}
	}

	const _CLASS = __CLASS__;
	/** @return Df_YandexMarket_Model_Yml_Products */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}