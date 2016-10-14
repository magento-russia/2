<?php
class Df_Cms_Model_Resource_Increment extends Df_Core_Model_Resource {
	/**
	 * @param Mage_Core_Model_Abstract $object
	 * @param int $type
	 * @param int $node
	 * @param int $level
	 * @return bool
	 */
	public function loadByTypeNodeLevel(Mage_Core_Model_Abstract $object, $type, $node, $level) {
		$select = rm_select()->from($this->getMainTable())
			->forUpdate(true)
			->where('? = type', $type)
			->where('? = node', $node)
			->where('? = level', $level)
		;
		$data = rm_conn()->fetchRow($select);
		if (!$data) {
			return false;
		}
		$object->setData($data);
		$this->_afterLoad($object);
		return true;
	}

	/**
	 * @used-by Df_Cms_Observer::cms_page_delete_after()
	 * @used-by Df_Cms_Model_Page_Version::_afterDelete()
	 * @param int $type
	 * @param int $node
	 * @param int $level
	 * @return Df_Cms_Model_Resource_Increment
	 */
	public function cleanIncrementRecord($type, $node, $level) {
		$write = $this->_getWriteAdapter();
		$write->delete($this->getMainTable(), array(
			'type = ?' => $type, 'node = ?' => $node, 'level = ?' => $level
		));
		return $this;
	}

	/**
	 * Нельзя вызывать @see parent::_construct(),
	 * потому что это метод в родительском классе — абстрактный.
	 * @see Mage_Core_Model_Mysql4_Abstract::_construct()
	 * @override
	 * @return void
	 */
	protected function _construct() {$this->_init(self::TABLE, Df_Cms_Model_Increment::P__ID);}
	const _C = __CLASS__;
	/** @used-by Df_Cms_Setup_2_0_0::_process() */
	const TABLE = 'df_cms/increment';
	/** @return Df_Cms_Model_Resource_Increment */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}