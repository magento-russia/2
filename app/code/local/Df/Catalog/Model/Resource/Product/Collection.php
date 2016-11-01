<?php
class Df_Catalog_Model_Resource_Product_Collection 
	extends Mage_Catalog_Model_Resource_Product_Collection {
	/**
	 * @override
	 * @param Mage_Core_Model_Resource_Abstract|array(string => mixed) $resource
	 */
	public function __construct($resource = null) {
		if (is_array($resource)) {
			$this->_rmData = $resource;
			$resource = null;
		}
		parent::__construct($resource);
	}

	/** @return array(string => Df_Catalog_Model_Resource_Eav_Attribute) */
	public function getAttributes() {
		if (!isset($this->{__METHOD__})) {
			/** @var array(string => Df_Catalog_Model_Resource_Eav_Attribute) $result */
			$result = array();
			foreach ($this->getAttributeSets() as $attributeSet) {
				/** @var Df_Eav_Model_Entity_Attribute_Set $attributeSet */
				foreach ($attributeSet->getAttributes() as $attribute) {
					/** @var Df_Catalog_Model_Resource_Eav_Attribute $attribute */
					if ($attribute->isApplicableToProductSystemType($this->getTypes())) {
						$result[$attribute->getName()] = $attribute;
					}
				}
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return array(int => Df_Eav_Model_Entity_Attribute_Set) */
	public function getAttributeSets() {
		if (!isset($this->{__METHOD__})) {
			/** @var array(int => Df_Eav_Model_Entity_Attribute_Set) $result */
			$result = array();
			/** @var Df_Dataflow_Model_Registry_Collection_AttributeSets $registry */
			$registry = Df_Dataflow_Model_Registry::s()->attributeSets();
			foreach ($this as $product) {
				/** @var Df_Catalog_Model_Product $product */
				/** @var int $attributeSetId */
				$attributeSetId = $product->getAttributeSetId();
				$result[$attributeSetId] = $registry->findById($attributeSetId);
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @param string|string[]|integer|Mage_Core_Model_Config_Element $attribute
	 * @param bool|string $joinType [optional]
	 * @return Df_Catalog_Model_Resource_Product_Collection
	 */
	public function addAttributeToSelect($attribute, $joinType = false) {
		if ($this->isEnabledFlat() && ('*' !== $attribute)) {
			$this->checkAttributesAreAvailableInFlatMode(df_array($attribute));
		}
		parent::addAttributeToSelect($attribute, $joinType);
		return $this;
	}

	/**
	 * @override
	 * @param string $attribute
	 * @param string $dir
	 * @return Df_Catalog_Model_Resource_Product_Collection
	 */
	public function addAttributeToSort($attribute, $dir = self::SORT_ORDER_ASC) {
		if (
				('position' === $attribute)
			&&
				!isset($this->_joinFields[$attribute])
			&&
				$this->isEnabledFlat()
			&&
				!$this->_catIndexPositionIsAvailable
		) {
			/**
			 * Заплатка для устранения сбоя
			 * Column not found: 1054 Unknown column 'cat_index_position' in 'order clause'
			 * Дело в том, что родительский метод при данных условиях
			 * использует колонку cat_index_position, которая может отсутствовать в наборе данных,
			 * и в таком случае добавляем её к набору данных.
			 */
			/**
			 * Копируем код из родительского метода, но без вызова
					if ($this->isEnabledFlat()) {
						$this->getSelect()->order("cat_index_position {$dir}");
					}
			 * который и приводил к сбою.
			 */
			$filters = $this->_productLimitationFilters;
			if (isset($filters['category_id']) || isset($filters['visibility'])) {
				$this->getSelect()->order('cat_index.position ' . $dir);
			}
			else {
				$this->getSelect()->order('e.entity_id ' . $dir);
			}
		}
		else {
			parent::addAttributeToSort($attribute, $dir);
		}
		return $this;
	}

	/**
	 * @param int[] $categoryIds
	 * @return Df_Catalog_Model_Resource_Product_Collection
	 */
	public function addCategoriesFilter(array $categoryIds) {
		df_param_array($categoryIds, 0);
		$this->_productLimitationFilters[self::$LIMITATION__CATEGORIES] = $categoryIds;
		if (Mage_Core_Model_App::ADMIN_STORE_ID === df_nat0($this->getStoreId())) {
			$this->_applyZeroStoreProductLimitations();
		} else {
			$this->_applyProductLimitations();
		}
		return $this;
	}

	/**
	 * Этот метод отличается от родительского метода addIdFilter тем,
	 * что фильтр по идентификаторам не добавляется к запросу SQL,
	 * а вместо этого фильтрация производится средствами PHP
	 * уже полученного от сервера базы данных множеcства записей.
	 *
	 * Фильтрация средствами PHP позволяет сократить размер запроса PHP.
	 * Это очень важно для модуля Яндекс.Маркет.
	 *
	 * Модуль Яндекс.Маркет позволяет администратору
	 * ограничить множество выгружаемых на Яндекс.Маркет товаров
	 * посредством правила, аналогичного по административному интерфейсу
	 * ценовому правилу для каталога.
	 *
	 * При этом удовлетворяющее правилу множество товаров может быть очень большим.
	 * Если использовать стандартный метод addIdFilter,
	 * то все удовлетворяющие правилу идентификаторы товаров будут добавлены
	 * в часть IN (...) запроса SQL.
	 *
	 * При этом, например, для магазина shop.soundmaster.ua
	 * запрос получается размером 1 мегабайт текста.
	 *
	 * Такие большие запросы могут приводить к сбоям сервера MySQL и интерпретатора PHP
	 * и требовать их специальной перенастройки.
	 *
	 * Фильтрация запроса средствами интерпретатора PHP прозволяет избежать этих проблем.
	 * @param int[] $productIds
	 * @return Df_Catalog_Model_Resource_Product_Collection
	 */
	public function addIdFilterClientSide(array $productIds) {
		$this->_idFilterClientSide = $productIds;
		return $this;
	}
	/** @var int[] */
	private $_idFilterClientSide = array();

	/**
	 * Возвращает идентификаторы всех товарных разделов коллекции.
	 * Использует алгоритм @see Mage_Catalog_Model_Resource_Product_Collection::addCategoryIds()
	 * Обратите внимание, что @see getItems() загружает коллекцию.
	 * Предварительная загрузка коллекции — необходимое условие работоспособности
	 * расположенного ниже алгоритма
	 * @return int[]
	 */
	public function getCategoryIds() {
		if (!isset($this->{__METHOD__})) {
			/** @var int[] $productIds */
			$productIds = array_keys($this->getItems());
			$this->{__METHOD__} = !$productIds ? array() : df_fetch_col_int_unique(
				$this->_productCategoryTable, 'category_id', 'product_id', $productIds
			);
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param string|null $paramName [optional]
	 * @return mixed
	 */
	public function getRmData($paramName = null) {
		return is_null($paramName) ?  $this->_rmData : dfa($this->_rmData, $paramName);
	}

	/**
	 * Отключение денормализации позволяет иметь в коллекции товаров все необходимые нам свойства.
	 * Вместо отключения денормализации есть и другой способ иметь все необходимые свойства:
	 * указать в установочном скрипте,
	 * что требуемые свойства должны попадать в коллекцию в режиме денормализации.
	 * @see Df_Shipping_Setup_2_16_2::process()
	 * Однако методу Df_C1_Cml2_Import_Processor_Product_Type::getDescription()
	 * требуется, чтобы в коллекции товаров присутствовало свойство «описание».
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
	 * @override
	 * @return bool
	 */
	public function isEnabledFlat() {
		/** @var bool $isFlatDisabled */
		$isFlatDisabled = (true === $this->getRmData(self::P__DISABLE_FLAT));
		return !$isFlatDisabled && parent::isEnabledFlat();
	}

	/**
	 * 2015-02-09
	 * @override
	 * @see Mage_Catalog_Model_Resource_Product_Collection::setEntity()
	 * @param Mage_Eav_Model_Entity_Abstract $entity
	 * @return void
	 */
	public function setEntity($entity) {df_should_not_be_here();}

	/**
	 * @override
	 * @return Df_Catalog_Model_Resource_Product_Collection
	 */
	protected function _afterLoad() {
		parent::_afterLoad();
		if ($this->_idFilterClientSide) {
			$this->_items = dfa_select($this->_items, $this->_idFilterClientSide);
		}
		/** @uses Df_Catalog_Model_Product::markAsLoadedInCollection() */
		df_each($this, 'markAsLoadedInCollection');
		return $this;
	}

	/**
	 * Apply limitation filters to collection
	 *
	 * Method allows using one time category product index table (or product website table)
	 * for different combinations of store_id/category_id/visibility filter states
	 *
	 * Method supports multiple changes in one collection object for this parameters
	 * @return Df_Catalog_Model_Resource_Product_Collection
	 */
	protected function _applyProductLimitations() {
		/**
		 * Обратите внимание,
		 * что данный метод может быть вызван несколько раз для одной и той же коллекции.
		 * Например, данный метод будет вызван при каждом вызове следующих методов коллекции:
		 * @used-by addMinimalPrice()
		 * @used-by addFinalPrice()
		 * @used-by addStoreFilter()
		 */
		// Добавил данный код 2014-10-07 из Magento CE 1.9.0.1
		Mage::dispatchEvent('catalog_product_collection_apply_limitations_before', array(
			'collection'  => $this,
			'category_id' => isset($this->_productLimitationFilters['category_id'])
			? $this->_productLimitationFilters['category_id']
			: null,
		));
		$this->_prepareProductLimitationFilters();
		$this->_productLimitationJoinWebsite();
		$this->_productLimitationJoinPrice();
		$filters = $this->_productLimitationFilters;
		if (
				isset($filters['category_id'])
			||
				isset($filters['visibility'])
			||
				$this->hasCategoriesFilter()
		) {
			/** @var string[] $conditions */
			$conditions = array('cat_index.product_id = e.entity_id');
			/**
			 * Ключ «store_id» может отсутствовать в массиве $filters.
			 * http://magento-forum.ru/topic/3748/
			 */
			/** @var int|null $storeId */
			$storeId = dfa($filters, 'store_id');
			if (!is_null($storeId)) {
				$conditions[]= df_db_quote_into('? = cat_index.store_id', $storeId);
			}
			if (
					isset($filters['visibility'])
				&&
					!isset($filters['store_table'])
			) {
				$conditions[]= df_db_quote_into('cat_index.visibility IN (?)', $filters['visibility']);
			}
			if ($this->hasCategoriesFilter()) {
				$conditions[]=
					!$this->getCategoriesFilter()
					? null
					: sprintf('cat_index.category_id IN (%s)', df_csv($this->getCategoriesFilter()))
				;
			}
			else {
				/** @var int|null $categoryId */
				$categoryId = dfa($filters, 'category_id');
				if ($categoryId && !$this->getFlag('disable_root_category_filter')) {
					$conditions[] = df_db_quote_into('? = cat_index.category_id', $categoryId);
				}
			}
			if (isset($filters['category_is_anchor'])) {
				$conditions[]= df_db_quote_into('? = cat_index.is_parent', $filters['category_is_anchor']);
			}
			/** @var string $joinCond */
			$joinCond = implode(' AND ', array_filter($conditions));
			/** @var array $fromPart */
			$fromPart = $this->getSelect()->getPart(Zend_Db_Select::FROM);
			/**
			 * При выборке товаров сразу по нескольким товарным разделам
			 * надо использовать DISTINCT,
			 * иначе при создании коллекции произойдёт исключительная ситуация.
			 */
			if ($this->hasCategoriesFilter()) {
				$this->getSelect()->distinct(true);
			}
			if (isset($fromPart['cat_index'])) {
				$fromPart['cat_index']['joinCondition'] = $joinCond;
				$this->getSelect()->setPart(Zend_Db_Select::FROM, $fromPart);
			}
			else {
				$this->getSelect()->join(
					array('cat_index' => $this->getTable('catalog/category_product_index'))
					,$joinCond
					/**
					 * Обратите внимание, что синтаксис array() указывает на то,
					 * что система не должна выбирать данные из связанной таблицы.
					 *
					 * Если при выборке по нескольким товарным разделам
					 * система бы выбирала ещё и данные по связанной таблице,
					 * то, при наличии одного товара сразу в нескольких товарных разделах
					 * DISTINCT бы не сработал,
					 * и система выбрала бы один и тот же товар несколько раз,
					 * что при добавлении товаров в коллекцию привело бы к исключительной ситуации.
					 *
					 * Пример на основе демо данных:
					 *
					 * [code]
						SELECT DISTINCT  `e` . * ,  `cat_pro`.`position` AS  `cat_index_position`
						FROM  `catalog_product_entity` AS  `e`
						INNER JOIN  `catalog_category_product` AS  `cat_pro`
						ON cat_pro.product_id = e.entity_id
						AND cat_pro.category_id
						IN( 3, 13, 8, 15, 27, 28 )
						LIMIT 0 , 30
					 * [/code]
					 *
					 * В этом примере DISTINCT работает не по идентификаторам товаров,
					 * а по комбинации идентификатора товара и поля `position` связанной таблицы,
					 * что приводит к выборке одного и того же товара несколько раз подряд.
					 */
					,$this->hasCategoriesFilter()
					? array()
					: array('cat_index_position' => 'position')
				);
			}
			$this->_productLimitationJoinStore();
			Mage::dispatchEvent('catalog_product_collection_apply_limitations_after', array(
				'collection' => $this
			));
		}
		return $this;
	}

	/**
	 * Apply limitation filters to collection base on API
	 *
	 * Method allows using one time category product table
	 * for combinations of category_id filter states
	 * @override
	 * @return Df_Catalog_Model_Resource_Product_Collection
	 */
	protected function _applyZeroStoreProductLimitations() {
		/** @var array $filters */
		$filters = $this->_productLimitationFilters;
		df_assert_array($filters);
		/** @var string $joinCond */
		$joinCond = df_ccc(' AND '
			,'cat_pro.product_id=e.entity_id'
			,!$this->hasCategoriesFilter()
			? df_db_quote_into('? = cat_pro.category_id', $filters['category_id'])
			: (
				!$this->getCategoriesFilter()
				? null
				: sprintf('cat_pro.category_id IN (%s)', df_csv($this->getCategoriesFilter()))
			)
		);
		/** @var array(string => mixed) $fromPart */
		$fromPart = $this->getSelect()->getPart(Zend_Db_Select::FROM);
		/** @var array|null $catPro */
		$catPro = dfa($fromPart, 'cat_pro');
		/**
		 * При выборке товаров сразу по нескольким товарным разделам
		 * надо использовать DISTINCT,
		 * иначе при создании коллекции произойдёт исключительная ситуация.
		 */
		if ($this->hasCategoriesFilter()) {
			$this->getSelect()->distinct(true);
		}
		if (!is_null($catPro)) {
			$fromPart['cat_pro']['joinCondition'] = $joinCond;
			$this->getSelect()->setPart(Zend_Db_Select::FROM, $fromPart);
		}
		else {
			$this->_catIndexPositionIsAvailable = !$this->hasCategoriesFilter();
			$this->getSelect()->join(
				array('cat_pro' => df_table('catalog/category_product'))
				,$joinCond
				/**
				 * Обратите внимание, что синтаксис array() указывает на то,
				 * что система не должна выбирать данные из связанной таблицы.
				 *
				 * Если при выборке по нескольким товарным разделам
				 * система бы выбирала ещё и данные по связанной таблице,
				 * то, при наличии одного товара сразу в нескольких товарных разделах
				 * DISTINCT бы не сработал,
				 * и система выбрала бы один и тот же товар несколько раз,
				 * что при добавлении товаров в коллекцию привело бы к исключительной ситуации.
				 *
				 * Пример на основе демо данных:
				 *
				 * [code]
					SELECT DISTINCT  `e` . * ,  `cat_pro`.`position` AS  `cat_index_position`
					FROM  `catalog_product_entity` AS  `e`
					INNER JOIN  `catalog_category_product` AS  `cat_pro`
					ON cat_pro.product_id = e.entity_id
					AND cat_pro.category_id
					IN( 3, 13, 8, 15, 27, 28 )
					LIMIT 0 , 30
				 * [/code]
				 *
				 * В этом примере DISTINCT работает не по идентификаторам товаров,
				 * а по комбинации идентификатора товара и поля `position` связанной таблицы,
				 * что приводит к выборке одного и того же товара несколько раз подряд.
				 */
				,!$this->_catIndexPositionIsAvailable
				? array()
				/**
				 * 2014-10-07
				 * Раньше тут стояло:
				 * array('cat_pro' => 'position')
				 * Новый код взял из Magento CE 1.9.0.1
				 */
				: array('cat_index_position' => 'position')
			);
		}
		// Добавил 2014-10-07 из Magento CE 1.9.0.1
		$this->_joinFields['position'] = array(
			'table' => 'cat_pro',
			'field' => 'position',
		);
		return $this;
	}

	/**
	 * @param string[] $attributes
	 * @return Df_Catalog_Model_Resource_Product_Collection
	 */
	private function checkAttributesAreAvailableInFlatMode(array $attributes) {
		if ($this->isEnabledFlat()) {
			/** @var Mage_Catalog_Model_Resource_Product_Flat_Indexer $productFlatIndexer */
			$productFlatIndexer = Mage::getResourceSingleton('catalog/product_flat_indexer');
			/** @var Mage_Catalog_Model_Resource_Product_Flat $resourceProductFlat */
			$resourceProductFlat = $this->getEntity();
			/** @var string[] $failedAttributes */
			$failedAttributes = array();
			foreach ($attributes as $attribute) {
				/** @var string $attribute */
				/**
				 * Раньше тут была проверка:
				 	if (!$resourceProductFlat->getAttributeForSelect($attribute))
				 * Этот код неверно работал для свойства «status»:
				 * getAttributeForSelect для этого свойства возвращало null.
				 *
				 * Обратите внимание, что проверку
				 * !$resourceProductFlat->getAttributeForSelect($attribute)
				 * убирать нельзя, потому что массив $productFlatIndexer->getAttributeCodes()
				 * не содержит свойств «entity_id», «type_id», «attribute_set_id».
				 */
				if (
						!in_array($attribute, $productFlatIndexer->getAttributeCodes())
					&&
						!$resourceProductFlat->getAttributeForSelect($attribute)
				) {
					$failedAttributes[]= $attribute;
				}
			}
			if ($failedAttributes) {
				if (1 === count($failedAttributes)) {
					df_warning(
						"Некий модуль или оформительская тема"
						." требует наличия товарного свойства «%s» в коллекции товаров,"
						." однако сейчас это свойство настроено таким образом,"
						." что оно в коллекцию товаров не попадёт."
						."\nЧтобы это товарное свойство попадало в коллекцию товаров, Вам надо сейчас открыть"
						." административный экран настроек данного товарного свойства"
						."\n(«Каталог» → «Типы и свойства» → «Свойства товаров»), указать «да»"
						." в качестве значения опции «Загружать ли в товарные коллекции?»"
						." и затем перестроить расчётные таблицы."
						,df_first($failedAttributes)
					);
				}
				else {
					df_warning(
						"Некий модуль или оформительская тема"
						." требует наличия товарных свойств %s в коллекции товаров,"
						." однако сейчас эти свойства настроены таким образом,"
						." что они в коллекцию товаров не попадут."
						."\nЧтобы эти товарные свойства попадали в коллекцию товаров, Вам надо сейчас открыть"
						." административный экран настроек этих товарных свойств"
						."\n(«Каталог» → «Типы и свойства» → «Свойства товаров»), указать «да»"
						." в качестве значения опции «Загружать ли в товарные коллекции?»"
						." и затем перестроить расчётные таблицы."
						,df_csv_pretty_quote($failedAttributes)
					);
				}
			}
		}
		return $this;
	}

	/** @return int[]|null */
	private function getCategoriesFilter() {
		return dfa($this->_productLimitationFilters, self::$LIMITATION__CATEGORIES);
	}

	/**
	 * Возвращает перечень системных типов товаров коллекции.
	 * Например:  array('simple', 'grouped', 'configurable', 'giftcard')
	 * @return string[]
	 */
	private function getTypes() {
		if (!isset($this->{__METHOD__})) {
			/** @uses Df_Catalog_Model_Product::getTypeId() */
			$this->{__METHOD__} = array_unique(df_each($this, 'getTypeId'));
		}
		return $this->{__METHOD__};
	}

	/** @return bool */
	private function hasCategoriesFilter() {return !is_null($this->getCategoriesFilter());}

	/**
	 * 2015-02-09
	 * Родительский метод не вызываем намеренно.
	 * @override
	 * @see Mage_Catalog_Model_Resource_Product_Collection::_init()
	 * @param string $model
	 * @param string|null $entityModel [optional]
	 * @return Df_Catalog_Model_Resource_Category_Collection
	 */
	protected function _init($model, $entityModel = null) {
		$this->_itemObjectClass = Df_Catalog_Model_Product::class;
		$this->_entity =
			$this->isEnabledFlat()
			? Df_Catalog_Model_Resource_Product_Flat::s()
			: Df_Catalog_Model_Resource_Product::s()
		;
		return $this;
	}

	/** @var bool */
	private $_catIndexPositionIsAvailable = false;
	/** @var array(string => mixed) */
	private $_rmData = array();
	/** @used-by Df_Catalog_Model_XmlExport_Catalog::_construct() */

	/** @var string */
	private static $LIMITATION__CATEGORIES = 'df_categories';
	/**
	 * Этот параметр используется модулем 1С:Управление торговлей.
	 * Отключение денормализации позволяет иметь в коллекции товаров все необходимые нам свойства.
	 * Вместо отключения денормализации есть и другой способ иметь все необходимые свойства:
	 * указать в установочном скрипте,
	 * что требуемые свойства должны попадать в коллекцию в режиме денормализации.
	 * @see Df_Shipping_Setup_2_16_2::process()
	 * Однако методу @see Df_C1_Cml2_Import_Processor_Product_Type::getDescription()
	 * требуется, чтобы в коллекции товаров присутствовало свойство «описание».
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
	const P__DISABLE_FLAT = 'disable_flat';
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Catalog_Model_Resource_Product_Collection
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}