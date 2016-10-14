<?php
class Df_Sales_Model_Resource_Order extends Mage_Sales_Model_Mysql4_Order {
	/**
	 * @param string $protectCode
	 * @return int
	 */
	public function getOrderIdByProtectCode($protectCode) {
		df_param_string($protectCode, 0);
		/** @var Zend_Db_Select $select */
		$select = $this->getReadConnection()->select()
			/**
			 * Используем $this->getTable('sales/order') вместо $this->getMainTable(),
			 * потому что в Magento CE 1.4
			 * другая иерархия классов-предков @see Df_Sales_Model_Resource_Order,
			 * в этой иерархии отсутствует класс @see Mage_Core_Model_Resource_Db_Abstract,
			 * и, соответственно, недоступен метод
			 * @see Mage_Core_Model_Resource_Db_Abstract::getMainTable()
			 */
			->from($this->getTable('sales/order'), 'entity_id')
			->where('? = protect_code', $protectCode)
		;
		return rm_nat0($this->getReadConnection()->fetchOne($select));
	}

	/** @return Df_Sales_Model_Resource_Order */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}