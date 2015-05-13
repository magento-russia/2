<?php
/**
 * @method Df_Cms_Model_Resource_Page getResource()
 */
class Df_Cms_Model_Resource_Page_Collection extends Mage_Cms_Model_Mysql4_Page_Collection {
	/**
	 * @override
	 * @param Mage_Core_Model_Resource_Db_Abstract|array(string => mixed) $resource
	 * @return Df_Cms_Model_Resource_Page_Collection
	 */
	public function __construct($resource = null) {
		if (is_array($resource)) {
			$this->_rmData = $resource;
			$resource = null;
		}
		parent::__construct($resource);
	}

	/**
	 * @param string|null $paramName [optional]
	 * @return mixed
	 */
	public function getRmData($paramName = null) {
		return is_null($paramName) ?  $this->_rmData : df_a($this->_rmData, $paramName);
	}

	/**
	 * Бывает, что демо-данные некоторых оформительских тем (например, SNS Xsport)
	 * содержат страницы, не привязанные ни к одному из магазинов,
	 * но при этом с идентификаторами, совпадающими с другими страницами.
	 * При сохранении этих страниц происходит сбой
	 * «Адрес страницы для указанного магазина уже существует»
	 * («A page URL key for specified store already exists.»)
	 * в методе @see Mage_Cms_Model_Resource_Page::_beforeSave()
	 * из-за того, что метод @see Mage_Cms_Model_Resource_Page::getIsUniquePageToStores()
	 * возвращает false.
	 * Нормально сохранять такие страницы-сироты всё равно нельзя,
	 * поэтому при массовой обратботке коллекции страниц с последующим сохранением
	 * отфильтровываем из такой коллекции страницы-сироты.
	 * @return Df_Cms_Model_Resource_Page_Collection
	 */
	public function withoutOrphans() {
		/** @var int[] $orphanIds */
		$orphanIds = $this->getResource()->findOrphanIds();
		if ($orphanIds) {
			$this->addFieldToFilter('page_id', array('nin' => $orphanIds));
		}
		return $this;
	}

	/**
	 * @override
	 * @return Df_Cms_Model_Resource_Page_Collection
	 */
	protected function _afterLoad() {
		parent::_afterLoad();
		/**
		 * Если Вы намерены изменять (обновлять, сохранять) элементы коллекции,
		 * то при создании коллекции укажите параметр load_stores_info = true,
		 * иначе при сохранении в многосайтовой системе возможен сбой:
		 * «A page URL key for specified store already exists»
		 * «Адрес страницы для указанного магазина уже существует»
		 * @see Mage_Cms_Model_Resource_Page::getIsUniquePageToStores()
		 */
		if ($this->needLoadStoresInfo()) {
			foreach ($this->_items as $page) {
				/** @var Df_Cms_Model_Page $page */
				$page->loadStoresInfo();
			}
		}
		return $this;
	}

	/**
	 * Если Вы намерены изменять (обновлять, сохранять) элементы коллекции,
	 * то при создании коллекции укажите параметр load_stores_info = true,
	 * иначе при сохранении в многосайтовой системе возможен сбой:
	 * «A page URL key for specified store already exists»
	 * «Адрес страницы для указанного магазина уже существует»
	 * @see Mage_Cms_Model_Resource_Page::getIsUniquePageToStores()
	 * @override
	 * @return bool
	 */
	private function needLoadStoresInfo() {return $this->getRmData(self::P__LOAD_STORES_INFO);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_init(Df_Cms_Model_Page::mf(), Df_Cms_Model_Resource_Page::mf());
	}
	/** @var array(string => mixed) */
	private $_rmData = array();
	const _CLASS = __CLASS__;
	/**
	 * Если Вы намерены изменять (обновлять, сохранять) элементы коллекции,
	 * то при создании коллекции укажите параметр load_stores_info = true,
	 * иначе при сохранении в многосайтовой системе возможен сбой:
	 * «A page URL key for specified store already exists»
	 * «Адрес страницы для указанного магазина уже существует»
	 * @see Mage_Cms_Model_Resource_Page::getIsUniquePageToStores()
	 */
	const P__LOAD_STORES_INFO = 'load_stores_info';

	/**
	 * @param bool $loadStoresInfo [optional]
	 * @return Df_Cms_Model_Resource_Page_Collection
	 */
	public static function i($loadStoresInfo = false) {
		return new self(array(self::P__LOAD_STORES_INFO => $loadStoresInfo));
	}
}