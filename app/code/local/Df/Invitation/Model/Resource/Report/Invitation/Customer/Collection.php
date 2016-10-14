<?php
class Df_Invitation_Model_Resource_Report_Invitation_Customer_Collection
	extends Mage_Reports_Model_Mysql4_Customer_Collection {
	/**
	 * Joins Invitation report data, and filter by date
	 *
	 * @param Zend_Date|string $from
	 * @param Zend_Date|string $to
	 * @return Df_Invitation_Model_Resource_Report_Invitation_Customer_Collection
	 */
	public function setDateRange($from, $to)
	{
		$this->_reset();
		$this->getSelect()
			->join(
				array('invitation' => df_table('df_invitation/invitation'))
				, 'invitation.customer_id = e.entity_id'
				, array(
					'sent' => new Zend_Db_Expr('COUNT(invitation.invitation_id)')
					, 'accepted' => new Zend_Db_Expr('COUNT(DISTINCT invitation.referral_id)')
		   		)
			)->group('e.entity_id')
		;
		$this->_joinFields['invitation_store_id'] = array('table'=>'invitation', 'field' => 'store_id');
		$this->_joinFields['invitation_date'] = array('table'=>'invitation', 'field' => 'date');
		// Filter by date range
		$this->addFieldToFilter('invitation_date', array('from' => $from, 'to' => $to, 'time'=>true));
		// Add customer name
		$this->addNameToSelect();
		// Add customer group
		$this->addAttributeToSelect('group_id', 'inner');
		$this->joinField('group_name', 'customer/customer_group', 'customer_group_code', 'customer_group_id=group_id');
		$this->orderByCustomerRegistration();
		return $this;
	}

	/**
	 * Filters report by stores
	 *
	 * @param array $storeIds
	 * @return Df_Invitation_Model_Resource_Report_Invitation_Customer_Collection
	 */
	public function setStoreIds($storeIds)
	{
		$vals = array_values($storeIds);
		if (count($storeIds) >= 1 && $vals[0] != '') {
			$this->addFieldToFilter('invitation_store_id', array('in' => (array)$storeIds));
		}
		return $this;
	}
}