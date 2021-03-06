<?php
/**
 * @method Df_Cms_Model_Resource_Block getResource()
 */
class Df_Cms_Model_Resource_Block_Collection extends Mage_Cms_Model_Mysql4_Block_Collection {
	/**
	 * @override
	 * @param Mage_Core_Model_Resource_Db_Abstract|array(string => mixed) $resource
	 * @return Df_Cms_Model_Resource_Block_Collection
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
	 * Метод выполнен по аналогии с методом
	 * @see Df_Cms_Model_Resource_Page_Collection::withoutOrphans()
	 * @return Df_Cms_Model_Resource_Block_Collection
	 */
	public function withoutOrphans() {
		/** @var int[] $orphanIds */
		$orphanIds = $this->getResource()->findOrphanIds();
		if ($orphanIds) {
			$this->addFieldToFilter('block_id', array('nin' => $orphanIds));
		}
		return $this;
	}

	/**
	 * @override
	 * @return Df_Cms_Model_Resource_Block_Collection
	 */
	protected function _afterLoad() {
		parent::_afterLoad();
		/**
		 * По аналогии с @see Df_Cms_Model_Resource_Page_Collection
		 * Правда, не проверял, имеется ли при сохранении самодельных блоков без информации о витринах
		 * опасность такого же сбоя, как для самодельных страниц.
		 */
		if ($this->needLoadStoresInfo()) {
			foreach ($this->_items as $block) {
				/** @var Df_Cms_Model_Block $block */
				$block->loadStoresInfo();
			}
		}
		return $this;
	}

	/**
	 * По аналогии с @see Df_Cms_Model_Resource_Page_Collection
	 * Правда, не проверял, имеется ли при сохранении самодельных блоков без информации о витринах
	 * опасность такого же сбоя, как для самодельных страниц.
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
		$this->_init(Df_Cms_Model_Block::mf(), Df_Cms_Model_Resource_Block::mf());
	}
	/** @var array(string => mixed) */
	private $_rmData = array();
	const _CLASS = __CLASS__;
	/**
	 * По аналогии с @see Df_Cms_Model_Resource_Page_Collection
	 * Правда, не проверял, имеется ли при сохранении самодельных блоков без информации о витринах
	 * опасность такого же сбоя, как для самодельных страниц.
	 */
	const P__LOAD_STORES_INFO = 'load_stores_info';

	/**
	 * @param bool $loadStoresInfo [optional]
	 * @return Df_Cms_Model_Resource_Block_Collection
	 */
	public static function i($loadStoresInfo = false) {
		return new self(array(self::P__LOAD_STORES_INFO => $loadStoresInfo));
	}
}