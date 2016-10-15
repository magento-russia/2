<?php
/**
 * @method bool|null getIsNewPage()
 * @method Df_Cms_Model_Resource_Page getResource()
 * @method bool|null getUnderVersionControl()
 * @method Df_Cms_Model_Page setIsNewPage(bool $value)
 * @method Df_Cms_Model_Page setPublishedRevisionId($value)
 */
class Df_Cms_Model_Page extends Mage_Cms_Model_Page {
	/**
	 * @return Df_Cms_Model_Page
	 * @throws Exception
	 */
	public function deleteRm() {
		df_admin_begin();
		try {
			$this->delete();
		}
		catch (Exception $e) {
			df_admin_end();
			df_error($e);
		}
		df_admin_end();
		return $this;
	}

	/** @return void */
	public function loadStoresInfo() {
		if ($this->getId() && !$this->getData(self::P__STORES)) {
			/** @var int[] $stores */
			$stores = $this->getResource()->lookupStoreIds($this->getId());
			/** @var bool $hasDataChanges */
			$hasDataChanges = $this->hasDataChanges();
			$this->addData(array(
				self::P__STORES => $stores
				,self::P__STORE_ID => $stores
			));
			$this->setDataChanges($hasDataChanges);
		}
	}

	/**
	 * @override
	 * @return Df_Cms_Model_Resource_Page_Collection
	 */
	public function getResourceCollection() {return self::c();}

	/**
	 * @override
	 * @return Df_Cms_Model_Resource_Page
	 * 2016-10-14
	 * В родительском классе метод переобъявлен через PHPDoc,
	 * и поэтому среда разработки думает, что он публичен.
	 */
	/** @noinspection PhpHierarchyChecksInspection */
	protected function _getResource() {return Df_Cms_Model_Resource_Page::s();}

	/**
	 * @used-by Df_Cms_Model_Resource_Page_Collection::_construct()
	 * @used-by Df_Localization_Onetime_Dictionary_Rule_Conditions_Page::getEntityClass()
	 * @used-by Df_Localization_Onetime_Processor_Cms_Page::_construct()
	 */
	const _C = __CLASS__;
	const P__IDENTIFIER = 'identifier';
	const P__IS_ACTIVE = 'is_active';
	const P__STORE_ID = 'store_id';
	const P__STORES = 'stores';
	const P__TITLE = 'title';

	/**
	 * @param bool $loadStoresInfo [optional]
	 * @return Df_Cms_Model_Resource_Page_Collection
	 */
	public static function c($loadStoresInfo = false) {
		return Df_Cms_Model_Resource_Page_Collection::i($loadStoresInfo);
	}
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Cms_Model_Page
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
	/**
	 * @static
	 * @param int|string $id
	 * @param string|null $field [optional]
	 * @return Df_Cms_Model_Page
	 */
	public static function ld($id, $field = null) {return df_load(self::i(), $id, $field);}

	/**
	 * @static
	 * @param string $urlKey
	 * @param string|null $title [optional]
	 * @return Df_Cms_Model_Page|null
	 */
	public static function loadByUrlKeyAndTitle($urlKey, $title = null) {
		/** @var Df_Cms_Model_Resource_Page_Collection $pages */
		$pages = self::c();
		$pages->addFieldToFilter(self::P__IDENTIFIER, $urlKey);
		if ($title) {
			$pages->addFieldToFilter(self::P__TITLE, $title);
		}
		$pages->getSelect()->limit(1);
		/** @var Df_Cms_Model_Page $result */
		$result = $pages->getFirstItem();
		/**
		 * Чтобы избежать сбоя при сохранении страницы:
		 * «A page URL key for specified store already exists»
		 * («Адрес страницы для указанного магазина уже существует»)
		 * @see Mage_Cms_Model_Resource_Page::getIsUniquePageToStores()
		 */
		$result->loadStoresInfo();
		return $result->getId() ? $result : null;
	}

	/** @return Df_Cms_Model_Page */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}