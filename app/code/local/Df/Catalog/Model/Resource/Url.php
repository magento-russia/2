<?php
class Df_Catalog_Model_Resource_Url extends Mage_Catalog_Model_Resource_Eav_Mysql4_Url {
	/**
	 * @param int[] $categoryIds
	 * @return array
	 */
	public function getCategoriesLevelInfo(array $categoryIds) {
		$result = array();
		$rowSet =
			$this->_getWriteAdapter()->fetchAll(
				(string)
					$this->_getWriteAdapter()->select()
						->from(rm_table('catalog/category'))
						->where('entity_id IN (?)', $categoryIds)
				)
		;
		foreach ($rowSet as $row) {
			$result[df_a($row, 'entity_id')] = df_a($row, 'level');
		}
		return $result;
	}

	/**
	 * Удаляем перенаправления для
	 * @param int $storeId [optional]
	 * @return Df_Catalog_Model_Resource_Url
	 */
	public function clearRewritesForInvisibleProducts($storeId = null) {
		if ($this->getInvisibleProductIds($storeId)) {
			/** @var array(string => mixed) $conditions */
			$conditions = array('product_id IN (?)' => $this->getInvisibleProductIds($storeId));
			if (!is_null($storeId)) {
				$conditions['? = store_id'] = $storeId;
			}
			$this->_getWriteAdapter()->delete($this->getMainTable(), $conditions);
		}
		return $this;
	}

	/**
	 * Finds and deletes old rewrites for store
	 * a) category rewrites left from the times when store had some other root category
	 * b) product rewrites left from products that once belonged to this site, but then deleted or just removed from website
	 *
	 * @override
	 * @param int $storeId
	 * @return Mage_Catalog_Model_Resource_Url|Mage_Catalog_Model_Resource_Eav_Mysql4_Url
	 */
	public function clearStoreInvalidRewrites($storeId)
	{
		if (df_enabled(Df_Core_Feature::SEO, $storeId)) {
			/**
			 * @todo Для подтоваров (вариантов для настраиваемых товаров)
			 * мы можем сделать перенаправление на настраиваемый товар — это самое разумное
			 */
			$this->clearRewritesForInvisibleProducts($storeId);
		}
		return parent::clearStoreInvalidRewrites($storeId);
	}

	/**
	 * @param int $storeId[optional]
	 * @return Df_Catalog_Model_Resource_Url
	 */
	public function clearSystemRewrites($storeId = null) {
		$condition = rm_quote_into('? = is_system', 1);
		if ($storeId !== null) {
			$condition .= rm_quote_into(' AND store_id IN (?)', $storeId);
		}
		$this->_getWriteAdapter()->delete($this->getMainTable(), $condition);
		return $this;
	}

	/**
	 * Result rewrites are grouped by product
	 *
	 * @param array $productIds
	 * @param int $storeId
	 * @return array
	 */
	public function getRewritesForProducts(array $productIds, $storeId) {
		$result = array();
		/** @var Zend_Db_Statement_Pdo $query */
		$query =
			$this
				->_getWriteAdapter()
					->query(
						(string)
							$this
								->_getWriteAdapter()
								->select()
								->from($this->getMainTable())
								->where('store_id=?', $storeId)
								->where('is_system=?', 1)
								->where('product_id IN (?)', $productIds)
								->order('product_id')
					)
		;
		while (true) {
			$row = $query->fetch();
			if (!$row) {
				break;
			}

			$rewrite = new Varien_Object($row);
			$rewrite->setIdFieldName($this->getIdFieldName());
			$productId = $rewrite->getData('product_id');
			if (!isset($result[$productId])) {
				$result[$productId] = array();
			}
			$result[$productId][]= $rewrite;
		}
		return $result;
	}

	/**
	 * @param array $params
	 * @return Df_Catalog_Model_Url
	 */
	public function makeRedirect(array $params) {
		$rewriteFrom = df_a($params, 'from');
		/** @var Varien_Object $rewriteFrom */

		$rewriteTo = df_a($params, 'to');
		/** @var Varien_Object $rewriteTo */
		df_assert($rewriteFrom->getData('request_path') != $rewriteTo->getData('request_path'));
		$this->_getWriteAdapter()->update(
			$this->getMainTable()
			,array(
				'options' => 'RP'
				,'target_path' => $rewriteTo->getData('request_path')
			)
			,rm_quote_into($this->getIdFieldName() . '=?', $rewriteFrom->getId())
		);
		$this->relinkRewrites($params);
		return $this;
	}

	/**
	 * Save rewrite URL
	 *
	 * @param array $rewriteData
	 * @param Varien_Object $rewrite
	 * @return Mage_Catalog_Model_Resource_Url|Mage_Catalog_Model_Resource_Eav_Mysql4_Url
	 */
	public function saveRewrite($rewriteData, $rewrite)
	{
		parent::saveRewrite($rewriteData, $rewrite);
		// В старых версиях Magento (в частности, Magento 1.4.0.1)
		// отсутствует функция автоматического перенаправления старых адресов на новые
		// при изменении адресного ключа (в новых версиях эта функция присутствует
		// и называется «Create Permanent Redirect for old URLs if Url key changed»).
		//
		// Т.к. эта функция очень важна при смене адресов на кириллические,
		// мы поддерживаем её, даже если она отсутствует в стандартной сборке

		/** @var bool[] $needSaveRewriteHistoryPatch */
		static $needSaveRewriteHistoryPatch = array();
		/** @var int $storeId */
		$storeId = $rewriteData['store_id'];
		if (!isset($needSaveRewriteHistoryPatch[$storeId])) {
			$needSaveRewriteHistoryPatch[$storeId] =
					df_enabled(Df_Core_Feature::SEO, $storeId)
				&&
					!method_exists($this, 'saveRewriteHistory')
				/**
				 * Раньше тут ещё стояла проверка
				 * на включенность соответствующей опции администратором.
				 * Теперь переключатель этой опции убрал,
				 * и опция включена всегда для старых версий Magento
				 * (за 2 года не припомню случаев, когда её надо было бы отключать).
				 * Тем самым административный интерфейс упрощён удалением малозначимой
				 * и редкой (только для Magento CE 1.4.0.1) опции
				 */
			;
		}
		if ($needSaveRewriteHistoryPatch[$storeId]) {
			$this->saveRewriteHistory_DfLegacyPatch($rewriteData, $rewrite);
		}
		return $this;
	}

	/**
	 * Перекрыл родительский метод с целью заместить
	 * опасную для кириллических веб-адресов функцию @see substr() на @see mb_substr().
	 *
	 * Использование substr() в другой аналогичной ситуации привело к падению интерпретатора PHP:
	 * @see Df_Catalog_Model_Url::getProductRequestPath().
	 *
	 * Обратите внимание, что мы не можем решить данную проблему посредством mbstring.func_overload,
	 * потому что значение mbstring.func_overload можно итзменить только через ini-файлы,
	 * и нельзя изменить через @see ini_set():
	 * @link http://stackoverflow.com/questions/8526147/utf-8-and-php-mbstring-func-overload-doesnt-work
	 *
	 * Реализацию данного метода я взял из Magento CE 1.9.0.1,
	 * но при этом проанализировал и реализацию
	 * из минимально поддерживаемой Российской сборкой Magento
	 * версии Magento CE 1.4.0.1: там реализация немного другая,
	 * но и реализация из Magento CE 1.9.0.1 вроде бы там должна работать.
	 *
	 * @override
	 * @param int|array $categoryIds
	 * @param int $storeId
	 * @param string $path
	 * @return array
	 */
	protected function _getCategories($categoryIds, $storeId = null, $path = null) {
		$isActiveAttribute = Mage::getSingleton('eav/config')
			->getAttribute(Mage_Catalog_Model_Category::ENTITY, 'is_active');
		$categories        = array();
		$adapter           = $this->_getReadAdapter();

		if (!is_array($categoryIds)) {
			$categoryIds = array($categoryIds);
		}
		/**
		 * Метод @see Varien_Db_Adapter_Pdo_Mysql::getCheckSql()
		 * отсутствует в Magento CE 1.4,
		 * поэтому используем вместо него
		 * @see Df_Core_Helper_Db::getCheckSql()
		 */
		$isActiveExpr = df()->db()->getCheckSql('c.value_id > 0', 'c.value', 'c.value');
		$select = $adapter->select()
			->from(array('main_table' => rm_table('catalog/category')), array(
				'main_table.entity_id',
				'main_table.parent_id',
				'main_table.level',
				'is_active' => $isActiveExpr,
				'main_table.path'
			))
		;

		// Prepare variables for checking whether categories belong to store
		if ($path === null) {
			$select->where('main_table.entity_id IN (?)', $categoryIds);
		} else {
		// Ensure that path ends with '/', otherwise we can get wrong results - e.g. $path = '1/2' will get '1/20'
		if (mb_substr($path, -1) != '/') {
			$path .= '/';
		}

		$select
			->where('main_table.path LIKE ?', $path . '%')
			->order('main_table.path');
		}
		/**
		 * Нельзя использовать rm_table(array('catalog/category', 'int')),
		 * потому что параметр-массив не поддерживается в Magento CE 1.4.
		 * @see Mage_Core_Model_Resource::getTableName()
		 */
		/** @var string $table */
		$table = rm_table('catalog_category_entity_int');
		$select->joinLeft(array('d' => $table),
				'd.attribute_id = :attribute_id AND d.store_id = 0 AND d.entity_id = main_table.entity_id',
				array()
			)
			->joinLeft(array('c' => $table),
				'c.attribute_id = :attribute_id AND c.store_id = :store_id AND c.entity_id = main_table.entity_id',
				array()
			)
		;

		if ($storeId !== null) {
			$rootCategoryPath = $this->getStores($storeId)->getRootCategoryPath();
			$rootCategoryPathLength = mb_strlen($rootCategoryPath);
		}
		$bind = array(
			'attribute_id' => (int)$isActiveAttribute->getId(),
			'store_id'     => (int)$storeId
		);

		$rowSet = $adapter->fetchAll($select, $bind);
		foreach ($rowSet as $row) {
			if ($storeId !== null) {
				// Check the category to be either store's root or its descendant
				// First - check that category's start is the same as root category
				if (mb_substr($row['path'], 0, $rootCategoryPathLength) != $rootCategoryPath) {
					continue;
				}
				// Second - check non-root category - that it's really a descendant, not a simple string match
				if ((mb_strlen($row['path']) > $rootCategoryPathLength)
					&& ($row['path'][$rootCategoryPathLength] != '/')) {
					continue;
				}
			}

			$category = new Varien_Object($row);
			$category->setIdFieldName('entity_id');
			$category->setStoreId($storeId);
			$this->_prepareCategoryParentId($category);

			$categories[$category->getId()] = $category;
		}
		unset($rowSet);

		if ($storeId !== null && $categories) {
			foreach (array('name', 'url_key', 'url_path') as $attributeCode) {
				$attributes = $this->_getCategoryAttribute($attributeCode, array_keys($categories),
				$category->getStoreId());
				foreach ($attributes as $categoryId => $attributeValue) {
					$categories[$categoryId]->setData($attributeCode, $attributeValue);
				}
			}
		}

		return $categories;
	}

	/**
	 * Return unique string based on the time in microseconds.
	 * @return string
	 */
	private function generateUniqueIdPath_DfLegacyPatch() {
		return str_replace('0.', '', str_replace(' ', '_', microtime()));
	}

	/**
	 * @param int $storeId
	 * @return array
	 */
	private function getInvisibleProductIds($storeId) {
		if (!isset($this->{__METHOD__}[$storeId])) {
			$this->{__METHOD__}[$storeId] =
				Mage::getResourceModel('catalog/product_collection')
					->setStoreId($storeId)
					->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE)
					->getAllIds()
			;
		}
		return $this->{__METHOD__}[$storeId];
	}

	/**
	 * Чтобы избежать нескольких перенаправлений подряд,
	 * мы смотрим, кто ссылается на $rewriteFrom,
	 * и переводим стрелки на $rewriteTo
	 *
	 * @param array $params
	 * @return Df_Catalog_Model_Resource_Url
	 */
	private function relinkRewrites(array $params) {
		/** @var Varien_Object $rewriteFrom */
		$rewriteFrom = df_a($params, 'from');
		/** @var Varien_Object $rewriteTo */
		$rewriteTo = df_a($params, 'to');
		$where = rm_quote_into('(target_path=?)', $rewriteFrom->getData('request_path'));
		if ($rewriteFrom->getData('store_id')) {
			$where .= rm_quote_into(' AND (store_id=?)', $rewriteFrom->getData('store_id'));
		}
		$where .= rm_quote_into(' AND (request_path<>?)', $rewriteTo->getData('request_path'));
		$this->_getWriteAdapter()->update(
			$this->getMainTable()
			,array('target_path' => $rewriteTo->getData('request_path'))
			,$where
		);
		return $this;
	}

	/**
	 * @param array $rewriteData
	 * @param Varien_Object $rewrite
	 * @return Mage_Catalog_Model_Resource_Url|Mage_Catalog_Model_Resource_Eav_Mysql4_Url
	 */
	private function saveRewriteHistory_DfLegacyPatch($rewriteData, $rewrite) {
		if ($rewrite instanceof Varien_Object && $rewrite->getId()) {
			$rewriteData['target_path'] = $rewriteData['request_path'];
			$rewriteData['request_path'] = $rewrite->getRequestPath();
			$rewriteData['id_path'] = $this->generateUniqueIdPath_DfLegacyPatch();
			$rewriteData['is_system'] = 0;
			$rewriteData['options'] = 'RP'; // Redirect = Permanent
			$this->saveRewriteHistory_DfLegacyPatch2($rewriteData);
		}
	}

	/**
	 * @param array(string => mixed) $rewriteData
	 * @return Df_Catalog_Model_Resource_Url
	 */
	private function saveRewriteHistory_DfLegacyPatch2($rewriteData) {
		$rewriteData = new Varien_Object($rewriteData);
		// check if rewrite exists with save request_path
		$rewrite = $this->getRewriteByRequestPath($rewriteData->getRequestPath(), $rewriteData->getStoreId());
		if ($rewrite === false) {
			// create permanent redirect
			$this->_getWriteAdapter()->insert($this->getMainTable(), $rewriteData->getData());
		}
		return $this;
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		/**
		 * Обратите внимание, что,
		 * хотя единственное, что делает родительский метод
		 * @see Mage_Catalog_Model_Resource_Url::_construct() —
		 * это вызов того же самого метода @see Mage_Core_Model_Resource_Db_Abstract::_init(),
		 * но со своими параметрами, отказываться от вызова родительского метода нежелательно по 2 причинам:
		 * 1) реализация родительского метода может меняться в будущих версиях Magento Community Edition
		 * 2) метод @see Mage_Core_Model_Resource_Db_Abstract::_init() работает ПО-РАЗНОМУ,
		 * когда вызывается в первый и последующие разы:
		 * он вызывает метод @see Mage_Core_Model_Resource_Db_Abstract::_setMainTable(),
		 * который содержит следущий код:
			if (empty($this->_resourceModel)) {
			    $this->_setResource($mainTableArr[0]);
		    }
		 * Этот код инициализирует свойство @see Mage_Core_Model_Resource_Db_Abstract::_resourceModel
		 * ТОЛЬКО ЕДИНОКРАТНО, при первом вызове.
		 * Свойство @see Mage_Core_Model_Resource_Db_Abstract::_resourceModel используется в программном коде
		 * класса @see Mage_Core_Model_Resource_Db_Abstract в единственном месте:
		 * в методе @see Mage_Core_Model_Resource_Db_Abstract::getTable(),
		 * который использует свойство @see Mage_Core_Model_Resource_Db_Abstract::_resourceModel
		 * для дополнения табличных имён, записанных в кратком виде:
		 * table_name вместо my_module_resource/table_name?
		 * и вот тогда @see Mage_Core_Model_Resource_Db_Abstract::getTable()
		 * подставляет @see Mage_Core_Model_Resource_Db_Abstract::_resourceModel
		 * вместо отсутствующей приставки my_module_resource.
		 */
		parent::_construct();
		$this->_init(self::TABLE_NAME, Df_Catalog_Model_Url::P__ID);
	}
	const _CLASS = __CLASS__;
	const TABLE_NAME = 'core/url_rewrite';

	/** @return string */
	public static function mf() {static $r; return $r ? $r : $r = rm_class_mf_r(__CLASS__);}
	/** @return Df_Catalog_Model_Resource_Url */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}