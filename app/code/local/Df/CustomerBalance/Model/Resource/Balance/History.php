<?php
class Df_CustomerBalance_Model_Resource_Balance_History extends Mage_Core_Model_Mysql4_Abstract {
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
	 * @override
	 * @return void
	 */
	protected function _construct() {
		/**
		 * Нельзя вызывать parent::_construct(),
		 * потому что это метод в родительском классе — абстрактный.
		 * @see Mage_Core_Model_Resource_Abstract::_construct()
		 */
		$this->_init(self::TABLE_NAME, Df_CustomerBalance_Model_Balance_History::P__ID);
	}
	const _CLASS = __CLASS__;
	const TABLE_NAME = 'df_customerbalance/balance_history';
	/**
	 * @see Df_CustomerBalance_Model_Balance_History::_construct()
	 * @see Df_CustomerBalance_Model_Resource_Balance_History_Collection::_construct()
	 * @return string
	 */
	public static function mf() {static $r; return $r ? $r : $r = rm_class_mf_r(__CLASS__);}
	/** @return Df_CustomerBalance_Model_Resource_Balance_History */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}