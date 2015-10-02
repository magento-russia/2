<?php
class Df_Catalog_Model_Resource_Product_Collection
	extends Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection {
	/**
	 * @override
	 * @param Mage_Core_Model_Resource_Abstract|array(string => mixed) $resource
	 * @return Df_Catalog_Model_Resource_Product_Collection
	 */
	public function __construct($resource = null) {
		if (is_array($resource)) {
			$this->_rmData = $resource;
			$resource = null;
		}
		parent::__construct($resource);
	}

	/**
	 * @override
	 * @param array|string|integer|Mage_Core_Model_Config_Element $attribute
	 * @param bool|string $joinType [optional]
	 * @return Df_Catalog_Model_Resource_Product_Collection
	 */
	public function addAttributeToSelect($attribute, $joinType = false) {
		if ($this->isEnabledFlat() && ('*' !== $attribute)) {
			$this->checkAttributesAreAvailableInFlatMode(
				is_array($attribute) ? $attribute : array($attribute)
			);
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
		if (Mage_Core_Model_App::ADMIN_STORE_ID === rm_nat0($this->getStoreId())) {
			$this->_applyZeroStoreProductLimitations();
		} else {
			$this->_applyProductLimitations();
		}
		return $this;
	}

	/**
	 * Метод Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection::addCategoryIds
	 * отсутствует в Magento CE 1.4.0.1
	 * @return Df_Catalog_Model_Resource_Product_Collection
	 */
	public function addCategoryIdsRm() {
		if (!$this->getFlag('category_ids_added')) {
			/** @var int[] $ids */
			$ids = array_keys($this->_items);
			if ($ids) {
				/** @var Zend_Db_Select $select */
				$select = $this->getConnection()->select();
				$select->from($this->_productCategoryTable, array('product_id', 'category_id'));
				$select->where('product_id IN (?)', $ids);
				/** @var array[] $data */
				$data = $this->getConnection()->fetchAll($select);
				/** @var array $categoryIds */
				$categoryIds = array();
				foreach ($data as $info) {
					/** @var string[] $info */
					if (isset($categoryIds[$info['product_id']])) {
						$categoryIds[$info['product_id']][]= $info['category_id'];
					} else {
						$categoryIds[$info['product_id']] = array($info['category_id']);
					}
				}
				foreach ($this->getItems() as $product) {
					/** @var Df_Catalog_Model_Product $product */
					/** @var bool $hasDataChanges */
					$hasDataChanges = $product->hasDataChanges();
					$product->setCategoryIds(df_a($categoryIds, $product->getId(), array()));
					$product->setDataChanges($hasDataChanges);
				}
				$this->setFlag('category_ids_added', true);
			}
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
	/** @var array */
	private $_idFilterClientSide = array();

	/**
	 * Возвращает идентификаторы всех товарных разделов коллекции.
	 * Использует алгоритм Mage_Catalog_Model_Resource_Product_Collection::addCategoryIds
	 * @see Mage_Catalog_Model_Resource_Product_Collection::addCategoryIds
	 * @return int[]
	 */
	public function getCategoryIds() {
		if (!isset($this->{__METHOD__})) {
			/** @var int[] $result */
			$result = array();
			/** @var int[] $productIds */
			$productIds =
				array_keys(
					/**
					 * Обратите внимание, что getItems() загружает коллекцию.
					 * Предварительная загрузка коллекции — необходимое условие работоспособности
					 * расположенного ниже алгоритма
					 */
					$this->getItems()
				)
			;
			if ($productIds) {
				/** @var Varien_Db_Select $select */
				$select = $this->getConnection()->select();
				$select
					->distinct($flag = true)
					->from(
						$name = $this->_productCategoryTable
						,$cols = array('category_id', 'product_id')
					)
					->where('product_id IN (?)', $productIds)
				;
				/** @var array $rows */
				$result = $this->getConnection()->fetchCol($select);
			}
			df_result_array($result);
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param string|null $paramName [optional]
	 * @return mixed
	 */
	public function getRmData($paramName = null) {
		return is_null($paramName) ?  $this->_rmData : df_a($this->_rmData, $paramName);
	}

	/**
	 * Отключение денормализации позволяет иметь в коллекции товаров все необходимые нам свойства.
	 * Вместо отключения денормализации есть и другой способ иметь все необходиые свойства:
	 * указать в установочном скрипте,
	 * что требуемые свойства должны попадать в коллекцию в режиме денормализации.
	 * @see Df_Shipping_Model_Setup_2_16_2::process()
	 * Однако методу Df_1C_Model_Cml2_Import_Processor_Product_Type::getDescription()
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
		/** @var bool $result */
		$result = !$isFlatDisabled && parent::isEnabledFlat();
		return $result;
	}

	/**
	 * @override
	 * @return Df_Catalog_Model_Resource_Product_Collection
	 */
	protected function _afterLoad() {
		parent::_afterLoad();
		if (0 < count($this->_idFilterClientSide)) {
			$this->_items =
				array_intersect_key(
					$this->_items
					,array_flip(
						$this->_idFilterClientSide
					)
				)
			;
		}
		return $this;
	}

	/**
	 * Apply limitation filters to collection
	 *
	 * Method allows using one time category product index table (or product website table)
	 * for different combinations of store_id/category_id/visibility filter states
	 *
	 * Method supports multiple changes in one collection object for this parameters
	 * @return Df_Catalog_Model_Resource_Product_Collection|Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
	 */
	protected function _applyProductLimitations() {
		/**
		 * Обратите внимание,
		 * что данный метод может быть вызван несколько раз для одной и той же коллекции.
		 * Например, данный метод будет вызван при каждолм вызове следующих методов коллекции:
		 * @see addMinimalPrice()
		 * @see addFinalPrice()
		 * @see addStoreFilter()
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
			 * @link http://magento-forum.ru/topic/3748/
			 */
			/** @var int|null $storeId */
			$storeId = df_a($filters, 'store_id');
			if (!is_null($storeId)) {
				$conditions[]= rm_quote_into('? = cat_index.store_id', $storeId);
			}
			if (
					isset($filters['visibility'])
				&&
					!isset($filters['store_table'])
			) {
				$conditions[]= rm_quote_into('cat_index.visibility IN (?)', $filters['visibility']);
			}
			if ($this->hasCategoriesFilter()) {
				$conditions[]=
					(1 > count($this->getCategoriesFilter()))
					? null
					: rm_sprintf(
						'cat_index.category_id IN (%s)', implode(',', $this->getCategoriesFilter())
					)
				;
			}
			else {
				/** @var int|null $categoryId */
				$categoryId = df_a($filters, 'category_id');
				if ($categoryId && !$this->getFlag('disable_root_category_filter')) {
					$conditions[] = rm_quote_into('? = cat_index.category_id', $categoryId);
				}
			}
			if (isset($filters['category_is_anchor'])) {
				$conditions[]= rm_quote_into('? = cat_index.is_parent', $filters['category_is_anchor']);
			}
			$conditions = df_clean($conditions);
			/** @var string $joinCond */
			$joinCond = implode(' AND ', $conditions);
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
	 * @return Df_Catalog_Model_Resource_Product_Collection|Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
	 */
	protected function _applyZeroStoreProductLimitations() {
		/** @var array $filters */
		$filters = $this->_productLimitationFilters;
		df_assert_array($filters);
		/** @var string $joinCond */
		$joinCond = rm_concat_clean(' AND '
			,'cat_pro.product_id=e.entity_id'
			,!$this->hasCategoriesFilter()
			? rm_quote_into('? = cat_pro.category_id', $filters['category_id'])
			: (
				!$this->getCategoriesFilter()
				? null
				: rm_sprintf('cat_pro.category_id IN (%s)', implode(',', $this->getCategoriesFilter()))
			)
		);
		/** @var array(string => mixed) $fromPart */
		$fromPart = $this->getSelect()->getPart(Zend_Db_Select::FROM);
		/** @var array|null $catPro */
		$catPro = df_a($fromPart, 'cat_pro');
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
				array('cat_pro' => rm_table('catalog/category_product'))
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
						,rm_first($failedAttributes)
					);
				}
				else {
					df_warning(
						"Некий модуль или оформительская тема"
						." требует наличия товарных свойств «%s» в коллекции товаров,"
						." однако сейчас эти свойства настроены таким образом,"
						." что они в коллекцию товаров не попадут."
						."\nЧтобы эти товарные свойства попадали в коллекцию товаров, Вам надо сейчас открыть"
						." административный экран настроек этих товарных свойств"
						."\n(«Каталог» → «Типы и свойства» → «Свойства товаров»), указать «да»"
						." в качестве значения опции «Загружать ли в товарные коллекции?»"
						." и затем перестроить расчётные таблицы."
						,implode(',', $failedAttributes)
					);
				}
			}
		}
		return $this;
	}

	/** @return array|null */
	private function getCategoriesFilter() {
		return df_a($this->_productLimitationFilters, self::$LIMITATION__CATEGORIES);
	}

	/** @return bool */
	private function hasCategoriesFilter() {return !is_null($this->getCategoriesFilter());}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_init(
			Df_Catalog_Model_Product::mf()
			,$this->isEnabledFlat()
			? Df_Catalog_Model_Resource_Product_Flat::mf()
			: Df_Catalog_Model_Resource_Product::mf()
		);
	}
	/** @var bool */
	private $_catIndexPositionIsAvailable = false;
	/** @var array(string => mixed) */
	private $_rmData = array();
	const _CLASS = __CLASS__;
	/** @var string */
	private static $LIMITATION__CATEGORIES = 'df_categories';
	/**
	 * Этот параметр используется модулем 1С:Управление торговлей.
	 * Отключение денормализации позволяет иметь в коллекции товаров все необходимые нам свойства.
	 * Вместо отключения денормализации есть и другой способ иметь все необходиые свойства:
	 * указать в установочном скрипте,
	 * что требуемые свойства должны попадать в коллекцию в режиме денормализации.
	 * @see Df_Shipping_Model_Setup_2_16_2::process()
	 * Однако методу Df_1C_Model_Cml2_Import_Processor_Product_Type::getDescription()
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