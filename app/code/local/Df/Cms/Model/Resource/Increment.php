<?php
class Df_Cms_Model_Resource_Increment extends Mage_Core_Model_Mysql4_Abstract {
	/**
	 * Load increment counter by passed node and level
	 *
	 * @param Mage_Core_Model_Abstract $object
	 * @param int $type
	 * @param int $node
	 * @param int $level
	 * @return bool
	 */
	public function loadByTypeNodeLevel(Mage_Core_Model_Abstract $object, $type, $node, $level)
	{
		$read = $this->_getReadAdapter();
		$select = $read->select()->from($this->getMainTable(), $cols = '*')
			->forUpdate(true)
			->where('type=?', $type)
			->where('node=?', $node)
			->where('level=?', $level);
		$data = $read->fetchRow($select);
		if (!$data) {
			return false;
		}

		$object->setData($data);
		$this->_afterLoad($object);
		return true;
	}

	/**
	 * Remove unneeded increment record.
	 *
	 * @param int $type
	 * @param int $node
	 * @param int $level
	 * @return Df_Cms_Model_Resource_Increment
	 */
	public function cleanIncrementRecord($type, $node, $level)
	{
		$write = $this->_getWriteAdapter();
		$write->delete($this->getMainTable(),array('type=?' => $type,'node=?' => $node,'level=?' => $level));
		return $this;
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		/**
		 * Нельзя вызывать parent::_construct(),
		 * потому что это метод в родительском классе — абстрактный.
		 * @see Mage_Core_Model_Resource_Abstract::_construct()
		 */
		$this->_init(self::TABLE_NAME, Df_Cms_Model_Increment::P__ID);
	}
	const _CLASS = __CLASS__;
	const TABLE_NAME = 'df_cms/increment';
	/**
	 * @see Df_Cms_Model_Increment::_construct()
	 * @return string
	 */
	public static function mf() {static $r; return $r ? $r : $r = rm_class_mf_r(__CLASS__);}
	/** @return Df_Cms_Model_Resource_Increment */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}