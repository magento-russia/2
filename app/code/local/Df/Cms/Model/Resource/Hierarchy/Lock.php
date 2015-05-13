<?php
class Df_Cms_Model_Resource_Hierarchy_Lock extends Mage_Core_Model_Mysql4_Abstract {
	/**
	 * Return last lock information
	 * @return array
	 */
	public function getLockData() {
		$select =
			$this->_getReadAdapter()->select()
				->from($this->getMainTable(), $cols = '*')
				->order('lock_id DESC')
				->limit(1)
		;
		$data = $this->_getReadAdapter()->fetchRow($select);
		return is_array($data) ? $data : array();
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
		$this->_init(self::TABLE_NAME, Df_Cms_Model_Hierarchy_Lock::P__ID);
	}
	const _CLASS = __CLASS__;
	const TABLE_NAME = 'df_cms/hierarchy_lock';
	/**
	 * @see Df_Cms_Model_Hierarchy_Lock::_construct()
	 * @return string
	 */
	public static function mf() {static $r; return $r ? $r : $r = rm_class_mf_r(__CLASS__);}
	/** @return Df_Cms_Model_Resource_Hierarchy_Lock */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}