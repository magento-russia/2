<?php
/**
 * @method string|null getDisplayMode()
 * @method Df_Catalog_Model_Category getParentCategory()
 * @method mixed getEvent()
 * @method bool|null getExcludeUrlRewrite()
 * @method bool|null getIsActive()
 * @method bool|null getIsAnchor()
 * @method string|null getName()
 * @method string|null getPath()
 * @method Df_Catalog_Model_Resource_Category|Df_Catalog_Model_Resource_Category_Flat getResource()
 * @method string|null getUrlKey()
 * @method Df_Catalog_Model_Category setDisplayMode(string $value)
 * @method Df_Catalog_Model_Category setExcludeUrlRewrite(bool $value)
 * @method Df_Catalog_Model_Category setIsActive(bool $value)
 * @method Df_Catalog_Model_Category setIsAnchor(bool $value)
 * @method Df_Catalog_Model_Category setName(string $value)
 * @method Df_Catalog_Model_Category setPath(string $value)
 * @method Df_Catalog_Model_Category setUrlKey(string $value)
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
	 * Чтобы не вызывать 1000 раз @see __call, я явно определил данный метод.
	 * Также заметил, что метод всегда возвращает одно и то же число («3»),
	 * потому что у товарных разделов, в отличие от товаров, нет прикладных типов.
	 * @override
	 * @return int
	 */
	public function getAttributeSetId() {static $r; return $r ? $r : $r = $this['attribute_set_id'];}

	/** @return string|null */
	public function get1CId() {return $this[Df_1C_Const::ENTITY_EXTERNAL_ID];}

	/**
	 * @override
	 * @return string
	 */
	public function getDescription() {
		/** @var string $result */
		$result = df_nts($this->_getData('description'));
		/** @var int $pageNumber */
		$pageNumber = df_nat0(rm_request('p'));
		if (
				1 < $pageNumber
			&&
				df_cfg()->seo()->catalog()->category()->needHideDescriptionFromNonFirstPages()
		) {
			$result = '';
		}
		return $result;
	}

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

	/**
	 * 2015-02-06
	 * По аналогии с @see Df_Catalog_Model_Product::getId()
	 * Читайте подробный комментарий в заголовке этого метода.
	 * @override
	 * @return int|null
	 */
	public function getId() {
		return isset($this->_data[self::P__ID]) ? (int)$this->_data[self::P__ID] : null;
	}

	/**
	 * 2015-02-09
	 * Родительский метод: @see Mage_Catalog_Model_Category::getResourceCollection()
	 * @override
	 * @return Df_Catalog_Model_Resource_Category_Collection|Df_Catalog_Model_Resource_Category_Flat_Collection
	 */
	public function getResourceCollection() {
		/** @var Df_Catalog_Model_Resource_Category_Collection|Df_Catalog_Model_Resource_Category_Flat_Collection $result */
		$result =
			$this->_useFlatResource
			? new Df_Catalog_Model_Resource_Category_Flat_Collection
			: new Df_Catalog_Model_Resource_Category_Collection
		;
		/** по аналогии с @see Mage_Catalog_Model_Category::getResourceCollection() */
		$result->setStoreId($this->getStoreId());
		return $result;
	}

	/** @return string */
	public function getTitle() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = strtr('«{name}» [{id}]', array(
				'{name}' => $this->getName(), '{id}' => $this->getId()
			));
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @param string $str
	 * @return string
	 */
	public function formatUrlKey($str) {
		/** @var bool $needFormat */
		static $needFormat;
		if (!isset($needFormat)) {
			$needFormat = df_cfg()->seo()->common()->getEnhancedRussianTransliteration();
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
	 *
	 * 2015-01-20
	 * Обратите внимание, что параметр $store — обязательный:
	 * неуказание магазина в многомагазинной системе
	 * при включенном режиме денормализации приводит в Magento CE 1.9.1.0 к сбою:
	 * «SQLSTATE[42S02]: Base table or view not found:
	 * Table 'catalog_category_flat' doesn't exist,
	 * query was: DESCRIBE `catalog_category_flat`».
	 * И действительно, таблица «catalog_category_flat» в БД отсутствует,
	 * вместо неё присутствуют таблицы:
	 * «catalog_category_flat_store_1»
	 * «catalog_category_flat_store_2»
	 * «catalog_category_flat_store_3»
	 * «catalog_category_flat_store_4»
	 *
	 * @param Df_Core_Model_StoreM|string|int|bool $store
	 * @return Df_Catalog_Model_Category
	 */
	public function saveRm($store) {
		/** поле @see _useFlatResource присутствует в том числе и в Magento CE 1.4.0.1 */
		if ($this->_useFlatResource) {
			df_error(
				'Программист пытается сохранять товарный раздел {category},'
				. ' загруженный из базы данных при включенном режиме денормализации.'
				. "\nMagento корректно сохраняет только те товарные разделы,"
				. " которые были загружены из базы данных при выключенном режиме денормализации."
				,array('{category}' => $this->getTitle())
			);
		}
		rm_admin_call($this, 'saveRmInternal', array($store));
		return $this;
	}

	/**
	 * 2015-01-20
	 * Обратите внимание, что параметр $store — обязательный:
	 * неуказание магазина в многомагазинной системе
	 * при включенном режиме денормализации приводит в Magento CE 1.9.1.0 к сбою:
	 * «SQLSTATE[42S02]: Base table or view not found:
	 * Table 'catalog_category_flat' doesn't exist,
	 * query was: DESCRIBE `catalog_category_flat`».
	 * И действительно, таблица «catalog_category_flat» в БД отсутствует,
	 * вместо неё присутствуют таблицы:
	 * «catalog_category_flat_store_1»
	 * «catalog_category_flat_store_2»
	 * «catalog_category_flat_store_3»
	 * «catalog_category_flat_store_4»
	 * @param Df_Core_Model_StoreM|string|int|bool $store
	 * @return void
	 */
	public function saveRmInternal($store) {
		$this->setStoreId(rm_store_id($store));
		$this->save();
	}

	/**
	 * @param string|null $value
	 * @return Df_Catalog_Model_Category
	 */
	public function set1CId($value) {
		$this->setData(Df_1C_Const::ENTITY_EXTERNAL_ID, $value);
		return $this;
	}

	/**
	 * 2015-02-09
	 * Родительский метод: @see Mage_Catalog_Model_Category::_getResource()
	 * @override
	 * @return Df_Catalog_Model_Resource_Category|Df_Catalog_Model_Resource_Category_Flat
	 */
	protected function _getResource() {
		return
			$this->_useFlatResource
			? Df_Catalog_Model_Resource_Category_Flat::s()
			: Df_Catalog_Model_Resource_Category::s()
		;
	}

	/**
	 * Родительский метод: @see Mage_Catalog_Model_Category::_construct()
	 * @override
	 * @return void
	 */
	protected function _construct() {
		/**
		 * 2015-02-09
		 * Намеренно убрал вызов родительского метода @see Mage_Catalog_Category::_construct().
		 */
		if (!Mage::isInstalled()) {
			/**
			 * Сюда мы попадаем при одновременной установке
			 * Magento CE 1.5.1.0 и Российской сборки Magento,
			 * поэтому надо инициализировать Российскую сборку Magento
			 * http://magento-forum.ru/topic/3732/
			 */
			Df_Core_Boot::run();
		}
		$this->_useFlatResource = self::useFlatResource() && !$this->_getData('disable_flat');
	}

	/**
	 * @used-by Df_1C_Cml2_Import_Processor_Category::_construct()
	 * @used-by Df_Catalog_Model_Resource_Category_Collection::_init()
	 * @used-by Df_Catalog_Model_Resource_Category_Flat_Collection::_construct()
	 * @used-by Df_Catalog_Model_XmlExport_Category::_construct()
	 * @used-by Df_Dataflow_Model_Registry_Collection_Categories::getEntityClass()
	 * @used-by Df_Localization_Onetime_Dictionary_Rule_Conditions_Category::getEntityClass()
	 * @used-by Df_Localization_Onetime_Processor_Catalog_Category::_construct()
	 */
	const _C = __CLASS__;
	const P__DISPLAY_MODE = 'display_mode';
	const P__EXCLUDE_URL_REWRITE = 'exclude_url_rewrite';
	const P__EXTERNAL_URL = 'rm__external_url';
	const P__ID = Mage_Eav_Model_Entity::DEFAULT_ENTITY_ID_FIELD;
	const P__IS_ACTIVE = 'is_active';
	const P__IS_ANCHOR = 'is_anchor';
	const P__NAME = 'name';
	const P__PATH = 'path';
	const P__THUMBNAIL = 'thumbnail';
	const P__URL_KEY = 'url_key';

	/**
	 * 2015-02-09
	 * По умолчанию денормализация здесь не используется
	 * ради совместимости с уже имеющимся программным кодом.
	 * Если кому-то нужно — пусть включит для своей коллекции вручную.
	 * @param bool $allowFlat [optional]
	 * @return Df_Catalog_Model_Resource_Category_Collection|Df_Catalog_Model_Resource_Category_Flat_Collection
	 */
	public static function c($allowFlat = false) {
		return
			$allowFlat && self::useFlatResource()
			? new Df_Catalog_Model_Resource_Category_Flat_Collection
			: new Df_Catalog_Model_Resource_Category_Collection
		;
	}

	/**
	 * 2015-01-20
	 * Обратите внимание, что параметр $storeId — обязательный:
	 * неуказание магазина в многомагазинной системе
	 * при включенном режиме денормализации приводит в Magento CE 1.9.1.0 к сбою:
	 * «SQLSTATE[42S02]: Base table or view not found:
	 * Table 'catalog_category_flat' doesn't exist,
	 * query was: DESCRIBE `catalog_category_flat`».
	 * И действительно, таблица «catalog_category_flat» в БД отсутствует,
	 * вместо неё присутствуют таблицы:
	 * «catalog_category_flat_store_1»
	 * «catalog_category_flat_store_2»
	 * «catalog_category_flat_store_3»
	 * «catalog_category_flat_store_4»
	 *
	 * @static
	 * @param array(string => mixed) $data
	 * @param int $storeId
	 * @return Df_Catalog_Model_Category
	 */
	public static function createAndSave(array $data, $storeId) {
		return self::i($data)->saveRm($storeId);
	}

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
		 */
		rm_admin_begin();
		/** @var Df_Catalog_Model_Category $result */
		try {
			$result = new self($parameters);
		}
		catch (Exception $e) {
			rm_admin_end();
			df_error($e);
		}
		rm_admin_end();
		return $result;
	}

	/**
	 * @static
	 * @param int|string $id
	 * @param Df_Core_Model_StoreM|int|string|bool|null $store [optional]
	 * @return Df_Catalog_Model_Category
	 */
	public static function ld($id, $store = null) {
		/** @var Df_Catalog_Model_Category $result */
		$result = self::i();
		if (!is_null($store)) {
			$result->setStoreId(rm_store_id($store));
		}
		return df_load($result, $id);
	}

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

	/**
	 * Нельзя кэшировать результат этого метода,
	 * потому что результат метода может меняться в зависимости от контекста:
	 * самый яркий пример — зависимость от вызова @see rm_admin_begin()
	 * (в административном режиме денормализация никогда не используется,
	 * а после выхода из административного режима денормализация может использоваться снова).
	 * @return bool
	 */
	private static function useFlatResource() {
		/** @var Mage_Catalog_Helper_Category_Flat $flatHelper */
		static $flatHelper;
		if (!$flatHelper) {
			$flatHelper = Mage::helper('catalog/category_flat');
		}
		/** @var bool $needUseOldHelperInterface */
		static $needUseOldHelperInterface;
		if (is_null($needUseOldHelperInterface)) {
			$needUseOldHelperInterface = df_magento_version('1.8.0.0', '<');
		}
		return
			!df_is_admin() &&
			($needUseOldHelperInterface
			? $flatHelper->isEnabled()
			: $flatHelper->isAvailable() && $flatHelper->isBuilt(true))
		;
	}
}