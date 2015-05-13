<?php
/**
 * @method Df_Cms_Model_Resource_Page getResource()
 */
class Df_Cms_Model_Page extends Mage_Cms_Model_Page {
	/**
	 * @return Df_Cms_Model_Page
	 * @throws Exception
	 */
	public function deleteRm() {
		rm_admin_begin();
		try {
			$this->delete();
		}
		catch(Exception $e) {
			rm_admin_end();
			throw $e;
		}
		rm_admin_end();
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
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_init(Df_Cms_Model_Resource_Page::mf());
	}
	const _CLASS = __CLASS__;
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
		 * «Адрес страницы для указанного магазина уже существует»
		 * @see Mage_Cms_Model_Resource_Page::getIsUniquePageToStores()
		 */
		$result->loadStoresInfo();
		return $result->getId() ? $result : null;
	}

	/**
	 * @see Df_Cms_Model_Resource_Page_Collection::_construct()
	 * @return string
	 */
	public static function mf() {static $r; return $r ? $r : $r = rm_class_mf(__CLASS__);}
	/** @return Df_Cms_Model_Page */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}