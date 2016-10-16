<?php
/**
 * @deprecated
 */
class Df_Catalog_Model_Convert_Adapter_Category extends Mage_Eav_Model_Convert_Adapter_Entity {	/** @var array */
	protected $_categoryCache = array();
	/** @var array */
	protected $_stores;

	/**
	 * Category display modes
	 * @var array
	 */
	protected $_displayModes = array(
		Mage_Catalog_Model_Category::DM_PRODUCT
		,Mage_Catalog_Model_Category::DM_PAGE
		,Mage_Catalog_Model_Category::DM_MIXED
	);

	/** @return void */
	public function parse()
	{
		$batchModel = df_mage()->dataflow()->batch();
		/** @var Mage_Dataflow_Model_Batch_Import $batchImportModel */
		$batchImportModel = $batchModel->getBatchImportModel();
		$importIds = $batchImportModel->getIdCollection();
		foreach ($importIds as $importId) {
			//print '<pre>'.memory_get_usage().'</pre>';
			$batchImportModel->load($importId);
			$importData = $batchImportModel->getBatchData();
			$this->saveRow($importData);
		}
	}

	/**
	 * Save category (import)
	 *
	 * @param array $importData
	 * @throws Mage_Core_Exception
	 * @return bool
	 */
	public function saveRow(array $importData)
	{
		$store = false;
		if (empty($importData['store'])) {
			if (!is_null($this->getBatchParams('store'))) {
				$store = df_store($this->getBatchParams('store'));
			} else {
				$message = df_mage()->catalogHelper()->__('Skip import row, required field "%s" not defined', 'store');
				Mage::throwException($message);
			}
		} else {
			$store = $this->getStoreByCode($importData['store']);
		}


		if ($store === false) {
			$message = df_mage()->catalogHelper()->__('Skip import row, store "%s" field not exists', $importData['store']);
			Mage::throwException($message);
		}

		$rootId = $store->getRootCategoryId();
		if (!$rootId) {
			return array();
		}
		$rootPath = '1/'.$rootId;
		if (empty($this->_categoryCache[$store->getId()])) {
			/** @var Df_Catalog_Model_Resource_Category_Collection $collection */
			$collection = Df_Catalog_Model_Category::c();
			$collection
					->setStore($store)
					->addAttributeToSelect('name')
			;
			$collection->getSelect()->where("path like '".$rootPath."/%'");
			foreach ($collection as $cat) {
				/** @var Df_Catalog_Model_Category $cat */
				$pathArr = df_explode_xpath($cat->getPath());
				$namePath = '';
				for ($i=2, $l=sizeof($pathArr); $i<$l; $i++) {
					$name = $collection->getItemById($pathArr[$i])->getName();
					$namePath .= (empty($namePath) ? '' : '/').trim($name);
				}
				$cat->setNamePath($namePath);
			}

			$cache = array();
			foreach ($collection as $cat) {
				$cache[mb_strtolower($cat->getNamePath())] = $cat;
				$cat->unsNamePath();
			}
			$this->_categoryCache[$store->getId()] = $cache;
		}
		$cache =& $this->_categoryCache[$store->getId()];
		$importData['categories'] = preg_replace('#\s*/\s*#', '/', trim($importData['categories']));
		if (!empty($cache[$importData['categories']])) {
			return true;
		}

		$path = $rootPath;
		$namePath = '';
		$i = 1;
		$categories = df_explode_xpath($importData['categories']);
		foreach ($categories as $catName) {
			$namePath .= (empty($namePath) ? '' : '/').mb_strtolower($catName);
			if (empty($cache[$namePath])) {
				$dispMode = $this->_displayModes[2];
				/**
				 * Перед созданием и сохранением товарного раздела
				 * надо обязательно надо установить текущим магазином административный, * иначе возникают неприятные проблемы.
				 *
				 * В частности, для успешного сохранения товарного раздела
				 * надо отключить на время сохранения режим денормализации.
				 * Так вот, в стандартном программном коде Magento автоматически отключает
				 * режим денормализации при создании товарного раздела из административного магазина
				 * (в конструкторе товарного раздела).
				 *
				 * А если сохранять раздел, чей конструктор вызван при включенном режиме денормализации —
				 * то произойдёт сбой:
				 *
				 * SQLSTATE[23000]: Integrity constraint violation:
				 * 1452 Cannot add or update a child row:
				 * a foreign key constraint fails
				 * (`catalog_category_flat_store_1`, * CONSTRAINT `FK_CAT_CTGR_FLAT_STORE_1_ENTT_ID_CAT_CTGR_ENTT_ENTT_ID`
				 * FOREIGN KEY (`entity_id`) REFERENCES `catalog_category_entity` (`en)
				 */
				$cache[$namePath] = Df_Catalog_Model_Category::createAndSave(array(
					Df_Catalog_Model_Category::P__PATH => $path
					,Df_Catalog_Model_Category::P__NAME => $catName
					,Df_Catalog_Model_Category::P__IS_ACTIVE => true
					,Df_Catalog_Model_Category::P__IS_ANCHOR => true
					,Df_Catalog_Model_Category::P__DISPLAY_MODE => $dispMode
				), $store->getId());
			}
			$catId = $cache[$namePath]->getId();
			$path .= '/'.$catId;
			$i++;
		}
		return true;
	}

	/**
	 * Retrieve store object by code
	 *
	 * @param string $store
	 * @return Df_Core_Model_StoreM
	 */
	public function getStoreByCode($store)
	{
		$this->_initStores();
		if (isset($this->_stores[$store])) {
			return $this->_stores[$store];
		}
		return false;
	}

	/**
	 *  Init stores
	 *
	 *  @param   none
	 *  @return	  void
	 */
	protected function _initStores() {
		if (is_null($this->_stores)) {
			$this->_stores = Mage::app()->getStores(true, true);
			foreach ($this->_stores as $code => $store) {
				/** @var Df_Core_Model_StoreM $store */
				$this->_storesIdCode[$store->getId()] = $code;
			}
		}
	}
	/**
	 * Как ни странно, переменная _storesIdCode
	 * используется в родительских методах без предварительного объявления:
	 * @used-by Mage_Catalog_Model_Convert_Adapter_Product::_initStores()
	 * @used-by Mage_Catalog_Model_Convert_Adapter_Product::getStoreById()
	 * @var array(int => string)
	 */
	protected $_storesIdCode = array();
}