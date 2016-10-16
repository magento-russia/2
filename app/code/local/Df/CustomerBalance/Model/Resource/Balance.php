<?php
class Df_CustomerBalance_Model_Resource_Balance extends Df_Core_Model_Resource {
	/**
	 * Delete customer orphan balances
	 * @param int $customerId
	 * @return Df_CustomerBalance_Model_Resource_Balance
	 */
	public function deleteBalancesByCustomerId($customerId) {
		$this->_getWriteAdapter()->delete(
			$this->getMainTable(), array('? = customer_id' => $customerId, 'website_id IS null')
		);
		return $this;
	}

	/**
	 * Get customer orphan balances count
	 * @param int $customerId
	 * @return Df_CustomerBalance_Model_Resource_Balance
	 */
	public function getOrphanBalancesCount($customerId) {
		$adapter = $this->_getReadAdapter();
		return $adapter->fetchOne($adapter->select()
			->from($this->getMainTable(), 'count(*)')
			->where('customer_id = ?', $customerId)
			->where('website_id IS null'));
	}

	/**
	 * Load customer balance data by specified customer id and website id
	 * @param Df_CustomerBalance_Model_Balance $object
	 * @param int $customerId
	 * @param int $websiteId
	 */
	public function loadByCustomerAndWebsiteIds($object, $customerId, $websiteId) {
		$data =
			$this->getReadConnection()->fetchRow(
				$this->getReadConnection()->select()
					->from($this->getMainTable())
					->where('customer_id = ?', $customerId)
					->where('website_id = ?', $websiteId)
					->limit(1)
			)
		;
		if ($data) {
			$object->addData($data);
		}
	}

	 /**
	 * Update customers balance currency code per website id
	 * @param int $websiteId
	 * @param string $currencyCode
	 * @return Df_CustomerBalance_Model_Resource_Balance
	 */
	public function setCustomersBalanceCurrencyTo($websiteId, $currencyCode) {
		$bind = array('base_currency_code' => $currencyCode);
		$this->_getWriteAdapter()
			->update(
				$this->getMainTable()
				,$bind
				,array('website_id=?' => $websiteId, 'base_currency_code IS null')
			)
		;
		return $this;
	}

	/**
	 * Нельзя вызывать @see parent::_construct(),
	 * потому что это метод в родительском классе — абстрактный.
	 * @see Mage_Core_Model_Resource_Abstract::_construct()
	 * @override
	 * @return void
	 */
	protected function _construct() {$this->_init(self::TABLE, Df_CustomerBalance_Model_Balance::P__ID);}
	/**
	 * @used-by Df_CustomerBalance_Model_Resource_Balance_History_Collection::_initSelect()
	 * @used-by Df_CustomerBalance_Setup_1_0_0::_process()
	 */
	const TABLE = 'df_customerbalance/balance';
	/** @return Df_CustomerBalance_Model_Resource_Balance */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}