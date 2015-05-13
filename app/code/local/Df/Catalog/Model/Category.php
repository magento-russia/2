<?php
/**
 * @method Df_Catalog_Model_Category getParentCategory()
 * @method Df_Catalog_Model_Resource_Category|Df_Catalog_Model_Resource_Category_Flat getResource()
 */
class Df_Catalog_Model_Category extends Mage_Catalog_Model_Category {
	/**
	 * Добавил этот метод ради ускорения работы системы.
	 * В классах-потомках метод @see getAttributeSetId отсутствует,
	 * и его реализация происходит через магический метод @see __call.
	 * Профилированием заметил, что при загрузке главной витринной страницы
	 * (оформительская тема Ultimo с демо-данными)
	 * метод @see getAttributeSetId вызывается более 1000 раз,
	 * занимая 0.31% общего времени создания страницы.
	 * Также не вызывать 1000 раз __call, я явно определил данный метод.
	 * Также заметил, что метод всегда возвращает одно и то же число («3»),
	 * потому что у товарных разделов, в отличие от товаров, нет прикладных типов.
	 * @override
	 * @return int
	 */
	public function getAttributeSetId() {
		/** @var int $result */
		static $result;
		if (!isset($result)) {
			$result = parent::_getData('attribute_set_id');
		}
		return $result;
	}

	/**
	 * @override
	 * @return string
	 */
	public function getDescription() {
		/** @var string $result */
		$result = df_nts(parent::_getData('description'));
		/** @var int $pageNumber */
		$pageNumber = rm_nat0(df_request('p'));
		if (
				(1 < $pageNumber)
			&&
				df_enabled(Df_Core_Feature::SEO)
			&&
				df_cfg()->seo()->catalog()->category()->needHideDescriptionFromNonFirstPages()
		) {
			$result = '';
		}
		return $result;
	}

	/** @return string|null */
	public function getDisplayMode() {return $this->_getData(self::P__DISPLAY_MODE);}

	/** @return bool */
	public function getExcludeUrlRewrite() {return !!$this->_getData(self::P__EXCLUDE_URL_REWRITE);}

	/** @return string|null */
	public function getExternalUrl() {return $this->_getData(self::P__EXTERNAL_URL);}
	
	/** @return Zend_Uri_Http|null */
	public function getExternalUri() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_n_set(
				!$this->getExternalUrl() ? null : Zend_Uri::factory($this->getExternalUrl())
			);
		}
		return rm_n_get($this->{__METHOD__});
	}

	/** @return bool */
	public function getIsActive() {return !!$this->_getData(self::P__IS_ACTIVE);}

	/** @return bool */
	public function getIsAnchor() {return !!$this->_getData(self::P__IS_ANCHOR);}

	/** @return string */
	public function getName() {return df_nts($this->_getData(self::P__NAME));}

	/** @return string|int|null */
	public function getPath() {return $this->_getData(self::P__PATH);}

	/** @return string|null */
	public function getUrlKey() {return $this->_getData(self::P__URL_KEY);}

	/**
	 * @override
	 * @param string $str
	 * @return string
	 */
	public function formatUrlKey($str) {
		/** @var bool $needFormat */
		static $needFormat;
		if (!isset($needFormat)) {
			$needFormat =
					df_enabled(Df_Core_Feature::SEO)
				&&
					df_cfg()->seo()->common()->getEnhancedRussianTransliteration()
			;
		}
		return
			$needFormat
			? Df_Catalog_Helper_Product_Url::s()->extendedFormat($str)
			: parent::formatUrlKey($str)
		;			
	}

	/** @return bool */
	public function isRoot() {return !$this->getParentId();}

	/**
	 * Перед сохранением товарного раздела
	 * надо обязательно надо установить текущим магазином административный,
	 * иначе возникают неприятные проблемы.
	 *
	 * Для успешного сохранения товарного раздела
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
	 * (`catalog_category_flat_store_1`,
	 * CONSTRAINT `FK_CAT_CTGR_FLAT_STORE_1_ENTT_ID_CAT_CTGR_ENTT_ENTT_ID`
	 * FOREIGN KEY (`entity_id`) REFERENCES `catalog_category_entity` (`en)
	 * @param int|null $storeId [optional]
	 * @return Df_Catalog_Model_Category
	 */
	public function saveRm($storeId = null) {
		rm_admin_call($this, 'saveRmInternal', array($storeId));
		return $this;
	}

	/**
	 * @param int|null $storeId [optional]
	 * @return void
	 */
	public function saveRmInternal($storeId = null) {
		if (!is_null($storeId)) {
			$this->setStoreId($storeId);
		}
		$this->save();
	}

	/**
	 * @param string $value
	 * @return Df_Catalog_Model_Category
	 */
	public function setDisplayMode($value) {
		df_param_string($value, 0);
		$this->setData(self::P__DISPLAY_MODE, $value);
		return $this;
	}

	/**
	 * @param bool|int $value
	 * @return Df_Catalog_Model_Category
	 */
	public function setExcludeUrlRewrite($value) {
		if (is_int($value)) {
			$value = (0 !== $value);
		}
		df_param_boolean ($value, 0);
		$this->setData(self::P__EXCLUDE_URL_REWRITE, $value);
		return $this;
	}

	/**
	 * @param bool|int $value
	 * @return Df_Catalog_Model_Category
	 */
	public function setIsActive($value) {
		if (is_int($value)) {
			$value = (0 !== $value);
		}
		df_param_boolean ($value, 0);
		$this->setData(self::P__IS_ACTIVE, $value);
		return $this;
	}

	/**
	 * @param bool $value
	 * @return Df_Catalog_Model_Category
	 */
	public function setIsAnchor($value) {
		df_param_boolean ($value, 0);
		$this->setData(self::P__IS_ANCHOR, $value);
		return $this;
	}

	/**
	 * @param string $value
	 * @return Df_Catalog_Model_Category
	 */
	public function setName($value) {
		df_param_string($value, 0);
		$this->setData(self::P__NAME, $value);
		return $this;
	}

	/**
	 * @param string|int $value
	 * @return Df_Catalog_Model_Category
	 */
	public function setPath($value) {
		if (is_int($value)) {
			$value = strval($value);
		}
		df_param_string($value, 0);
		$this->setData(self::P__PATH, $value);
		return $this;
	}

	/**
	 * @param string $value
	 * @return Df_Catalog_Model_Category
	 */
	public function setUrlKey($value) {
		df_param_string($value, 0);
		$this->setData(self::P__URL_KEY, $value);
		return $this;
	}

	/** @return Df_Catalog_Model_Category */
	public function unsetDisplayMode() {
		$this->unsetData(self::P__DISPLAY_MODE);
		return $this;
	}

	/** @return Df_Catalog_Model_Category */
	public function unsetIsActive() {
		$this->unsetData(self::P__IS_ACTIVE);
		return $this;
	}

	/** @return Df_Catalog_Model_Category */
	public function unsetIsAnchor() {
		$this->unsetData(self::P__IS_ANCHOR);
		return $this;
	}

	/** @return Df_Catalog_Model_Category */
	public function unsetName() {
		$this->unsetData(self::P__NAME);
		return $this;
	}

	/** @return Df_Catalog_Model_Category */
	public function unsetPath() {
		$this->unsetData(self::P__URL_KEY);
		return $this;
	}

	/** @return Df_Catalog_Model_Category */
	public function unsetUrlKey() {
		$this->unsetData(self::P__PATH);
		return $this;
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		/**
		 * Сюда мы попадаем при одновременной установке
		 * Magento CE 1.5.1.0 и Российской сборки Magento,
		 * поэтому надо инициализировать Российскую сборку Magento
		 * @link http://magento-forum.ru/topic/3732/
		 */
		if (!Mage::isInstalled()) {
			Df_Core_Bootstrap::s()->init();
		}
		/** @var Mage_Catalog_Helper_Category_Flat $flatHelper */
		static $flatHelper;
		if (!isset($flatHelper)) {
			$flatHelper = Mage::helper('catalog/category_flat');
		}
		/** @var bool $needEnableFlatHelper */
		static $needEnableFlatHelper;
		if (!isset($needEnableFlatHelper)) {
			$needEnableFlatHelper =
				df_magento_version('1.8.0.0', '<')
				? $flatHelper->isEnabled()
				: (
						$flatHelper->isAvailable()
					&&
						!Mage::app()->getStore()->isAdmin()
					&&
						$flatHelper->isBuilt(true)
				)
			;
		}
		if ($needEnableFlatHelper && !$this->_getData('disable_flat')) {
			$this->_init(Df_Catalog_Model_Resource_Category_Flat::mf());
			$this->_useFlatResource = true;
		} else {
			$this->_init(Df_Catalog_Model_Resource_Category::mf());
		}
	}
	const _CLASS = __CLASS__;
	const P__DISPLAY_MODE = 'display_mode';
	const P__EXCLUDE_URL_REWRITE = 'exclude_url_rewrite';
	const P__EXTERNAL_URL = 'rm__external_url';
	const P__IS_ACTIVE = 'is_active';
	const P__IS_ANCHOR = 'is_anchor';
	const P__NAME = 'name';
	const P__PATH = 'path';
	const P__THUMBNAIL = 'thumbnail';
	const P__URL_KEY = 'url_key';

	/** @return Df_Catalog_Model_Resource_Category_Collection */
	public static function c() {return self::s()->getCollection();}
	/**
	 * @static
	 * @param array(string => mixed) $data
	 * @param int $storeId
	 * @return Df_Catalog_Model_Category
	 */
	public static function createAndSave(array $data, $storeId) {return self::i($data)->saveRm($storeId);}
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Catalog_Model_Category
	 * @throws Exception
	 */
	public static function i(array $parameters = array()) {
		/**
		 * Перед созданием и сохранением товарного раздела
		 * надо обязательно надо установить текущим магазином административный,
		 * иначе возникают неприятные проблемы.
		 *
		 * Lля успешного сохранения товарного раздела
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
		 * (`catalog_category_flat_store_1`,
		 * CONSTRAINT `FK_CAT_CTGR_FLAT_STORE_1_ENTT_ID_CAT_CTGR_ENTT_ENTT_ID`
		 * FOREIGN KEY (`entity_id`) REFERENCES `catalog_category_entity` (`en)
		 */
		rm_admin_begin();
		/** @var Df_Catalog_Model_Category $result */
		$result = null;
		try {
			$result = new self($parameters);
		}
		catch (Exception $e) {
			rm_admin_end();
			throw $e;
		}
		rm_admin_end();
		return $result;
	}
	/**
	 * @static
	 * @param int|string $id
	 * @param int|null $storeId [optional]
	 * @return Df_Catalog_Model_Category
	 */
	public static function ld($id, $storeId = null) {
		/** @var Df_Catalog_Model_Category $result */
		$result = self::i();
		if (!is_null($storeId)) {
			$result->setStoreId($storeId);
		}
		return df_load($result, $id);
	}
	/**
	 * @see Df_Catalog_Model_Resource_Category_Collection::_construct()
	 * @see Df_Catalog_Model_Resource_Category_Flat_Collection::_construct()
	 * @return string
	 */
	public static function mf() {static $r; return $r ? $r : $r = rm_class_mf(__CLASS__);}
	/** @return void */
	public static function reindexFlat() {
		/**
		 * Если база данных находится в некорректном состоянии,
		 * то при денормализации таблиц товарных разделов может произойти сбой:
		 * «Undefined offset (index) in Mage/Catalog/Model/Resource/Category/Flat.php»:
		 * @see Df_Catalog_Model_Resource_Category_Flat::_getAttributeValues()
		 * Данный класс чинит базу данных.
		 */
		Df_Catalog_Model_Processor_DeleteOrphanCategoryAttributesData::s()->process();
		/**
		 * Константа @see Mage_Catalog_Helper_Category_Flat::CATALOG_FLAT_PROCESS_CODE
		 * отсутствует в Magento CE 1.4
		 */
		df_h()->index()->reindex('catalog_category_flat');
	}
	/** @return Df_Catalog_Model_Category */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}