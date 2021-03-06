<?php
class Df_Poll_Model_Resource_Poll_Collection extends Mage_Poll_Model_Mysql4_Poll_Collection {
	/**
	 * @override
	 * @param Mage_Core_Model_Resource_Db_Abstract|array(string => mixed) $resource
	 * @return Df_Poll_Model_Resource_Poll_Collection
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
	 * @override
	 * @return Df_Poll_Model_Resource_Poll_Collection
	 */
	protected function _afterLoad() {
		parent::_afterLoad();
		if ($this->needLoadStoresInfo()) {
			$this->addStoreData();
			foreach ($this->_items as $poll) {
				/** @var Df_Poll_Model_Poll $poll */
				$poll->setData(self::P__STORE_IDS, $poll->getData(self::P__STORES));
				$poll->setDataChanges(false);
			}
		}
		return $this;
	}

	/**
	 * При указании данного флага
	 * выполняется правильная загрузка информации о привязке опросов к товарным разделам.
	 * Стандартный метод
	 * @see addStoreData() немного наивен он загружает информации о привязке в поле «stores»
	 * @see Mage_Poll_Model_Resource_Poll_Collection::addStoreData()
			$item->setStores($storesToPoll[$item->getId()]);
	 * В то время как при сохранении опроса метод
	 * @see Mage_Poll_Model_Resource_Poll::_afterSave()
	 * смотрит информации о привязке в поле «store_ids».
	 * @override
	 * @return bool
	 */
	private function needLoadStoresInfo() {return $this->getRmData(self::P__LOAD_STORES_INFO);}

	/**
	 * Вынуждены сделать метод публичным, потому что публичен родительский.
	 * @override
	 * @return void
	 */
	public function _construct() {
		parent::_construct();
		$this->_init(Df_Poll_Model_Poll::mf(), Df_Poll_Model_Resource_Poll::mf());
	}
	/** @var array(string => mixed) */
	private $_rmData = array();
	const _CLASS = __CLASS__;
	const P__STORE_IDS = 'store_ids';
	const P__STORES = 'stores';
	/**
	 * При указании данного флага
	 * выполняется правильная загрузка информации о привязке опросов к товарным разделам.
	 * Стандартный метод
	 * @see addStoreData() немного наивен он загружает информации о привязке в поле «stores»
	 * @see Mage_Poll_Model_Resource_Poll_Collection::addStoreData()
			$item->setStores($storesToPoll[$item->getId()]);
	 * В то время как при сохранении опроса метод
	 * @see Mage_Poll_Model_Resource_Poll::_afterSave()
	 * смотрит информации о привязке в поле «store_ids».
	 */
	const P__LOAD_STORES_INFO = 'load_stores_info';
	/**
	 * @param bool $loadStoresInfo [optional]
	 * @return Df_Poll_Model_Resource_Poll_Collection
	 */
	public static function i($loadStoresInfo = false) {
		return new self(array(self::P__LOAD_STORES_INFO => $loadStoresInfo));
	}
} 