<?php
class Df_Invitation_Model_Resource_Report_Invitation_Collection
	extends Df_Invitation_Model_Resource_Invitation_Collection {
	/**
	 * Joins Invitation report data, and filter by date
	 *
	 * @param Zend_Date|string $from
	 * @param Zend_Date|string $to
	 * @return Df_Invitation_Model_Resource_Report_Invitation_Collection
	 */
	public function setDateRange($from, $to)
	{
		$this->_reset();
		$this->addFieldToFilter('date', array('from' => $from, 'to' => $to, 'time'=>true))
			->getSelect()
			->reset(Zend_Db_Select::COLUMNS)
			->columns(array(
				'sent' => new Zend_Db_Expr('COUNT(main_table.invitation_id)'),'accepted' => new Zend_Db_Expr('COUNT(DISTINCT main_table.referral_id)'),'canceled' => new Zend_Db_Expr('COUNT(DISTINCT IF(main_table.status = \'canceled\', main_table.invitation_id, null)) '),'canceled_rate' => new Zend_Db_Expr('COUNT(DISTINCT IF(main_table.status = \'canceled\', main_table.invitation_id, null)) / COUNT(main_table.invitation_id) * 100'),'accepted_rate' => new Zend_Db_Expr('COUNT(DISTINCT main_table.referral_id) / COUNT(main_table.invitation_id) * 100')
			))->group('("*")');
		$this->_joinFields($from, $to);
		return $this;
	}

	/**
	 * Join custom fields
	 * @return Df_Invitation_Model_Resource_Report_Invitation_Collection
	 */
	protected function _joinFields()
	{
		return $this;
	}

	/**
	 * Filters report by stores
	 *
	 * @param array $storeIds
	 * @return Df_Invitation_Model_Resource_Report_Invitation_Collection
	 */
	public function setStoreIds($storeIds)
	{
		$vals = array_values($storeIds);
		if (count($storeIds) >= 1 && $vals[0] != '') {
			$this->addFieldToFilter('main_table.store_id', array('in' => (array)$storeIds));
		}
		return $this;
	}
}