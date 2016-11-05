<?php
class Df_Cms_Model_Resource_Hierarchy_Lock extends Df_Core_Model_Resource {
	/**
	 * Return last lock information
	 * @return array
	 */
	public function getLockData() {
		return df_nta($this->_getReadAdapter()->fetchRow(
			$this->_getReadAdapter()->select()
				->from($this->getMainTable())
				->order('lock_id DESC')
				->limit(1)
		));
	}

	/**
	 * Нельзя вызывать @see parent::_construct(),
	 * потому что это метод в родительском классе — абстрактный.
	 * @see Mage_Core_Model_Resource_Abstract::_construct()
	 * @override
	 * @return void
	 */
	protected function _construct() {$this->_init(self::TABLE, Df_Cms_Model_Hierarchy_Lock::P__ID);}
	/** @used-by Df_Cms_Setup_2_0_0::_process() */
	const TABLE = 'df_cms/hierarchy_lock';
	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}