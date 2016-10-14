<?php
class Df_CustomerBalance_Model_Resource_Balance_History extends Df_Core_Model_Resource {
	/**
	 * @override
	 * @param Df_CustomerBalance_Model_Balance_History|Mage_Core_Model_Abstract $object
	 * @return Df_CustomerBalance_Model_Resource_Balance_History
	 */
	public function _beforeSave(Mage_Core_Model_Abstract $object) {
		$object->setUpdatedAt($this->formatDate(time()));
		parent::_beforeSave($object);
		return $this;
	}

	/**
	 * @param int $id
	 * @return Df_CustomerBalance_Model_Resource_Balance_History
	 */
	public function markAsSent($id) {
		$this->_getWriteAdapter()->update(
			$this->getMainTable()
			,array('is_customer_notified' => 1)
			,rm_quote_into('history_id = ?', $id)
		);
		return $this;
	}

	/**
	 * Нельзя вызывать @see parent::_construct(),
	 * потому что это метод в родительском классе — абстрактный.
	 * @see Mage_Core_Model_Mysql4_Abstract::_construct()
	 * @override
	 * @return void
	 */
	protected function _construct() {
		$this->_init(self::TABLE, Df_CustomerBalance_Model_Balance_History::P__ID);
	}
	/** @used-by Df_CustomerBalance_Setup_1_0_0::_process() */
	const TABLE = 'df_customerbalance/balance_history';
	/** @return Df_CustomerBalance_Model_Resource_Balance_History */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}