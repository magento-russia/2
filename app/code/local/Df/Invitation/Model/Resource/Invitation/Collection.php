<?php
class Df_Invitation_Model_Resource_Invitation_Collection extends Df_Core_Model_Resource_Collection {
	/** @return Df_Invitation_Model_Resource_Invitation_Collection */
	public function addCanBeCanceledFilter(){
		return $this->addFieldToFilter('status', array('nin' => array(
			Df_Invitation_Model_Invitation::STATUS_CANCELED
			,Df_Invitation_Model_Invitation::STATUS_ACCEPTED
		)));
	}

	/** @return Df_Invitation_Model_Resource_Invitation_Collection */
	public function addCanBeSentFilter() {
		return $this->addFieldToFilter('status', Df_Invitation_Model_Invitation::STATUS_NEW);
	}

	/** @return Df_Invitation_Model_Resource_Invitation_Collection */
	public function addInviteeInformation() {
		$this->getSelect()->joinLeft(
			array('c' => df_table('customer/entity'))
			,'main_table.referral_id = c.entity_id'
			,array('invitee_email' => 'c.email')
		);
		return $this;
	}

	/**
	 * @param array|int $storeIds
	 * @return Df_Invitation_Model_Resource_Invitation_Collection
	 */
	public function addStoreFilter($storeIds) {
		$this->getSelect()->where('main_table.store_id IN (?)', $storeIds);
		return $this;
	}

	/** @return Df_Invitation_Model_Resource_Invitation_Collection */
	public function addWebsiteInformation() {
		$this->getSelect()->joinInner(
			array('w' => df_table('core/store'))
			,'main_table.store_id = w.store_id'
			,'w.website_id'
		);
		return $this;
	}

	/**
	 * @override
	 * @return Df_Invitation_Model_Resource_Invitation
	 */
	public function getResource() {return Df_Invitation_Model_Resource_Invitation::s();}

	/**
	 * @param int $id
	 * @return Df_Invitation_Model_Resource_Invitation_Collection
	 */
	public function loadByCustomerId($id) {
		$this->getSelect()->where('main_table.customer_id = ?', $id);
		$this->load();
		return $this;
	}

	/**
	 * @override
	 * @return Df_Invitation_Model_Resource_Invitation_Collection
	 */
	protected function _initSelect() {
		$this->getSelect()->from(
			array('main_table' => $this->getResource()->getMainTable())
			,array(
				'*'
				,'invitation_email' => 'email'
				,'invitee_group_id' => 'group_id'
			)
		);
		return $this;
	}

	/**
	 * Обратите внимание, что область видимости данного поля должна быть именно protected,
	 * потому что от данного класса наследуются специализированные коллекции для отчётов:
	 * @used-by Df_Invitation_Model_Resource_Report_Invitation_Collection
	 * @used-by Df_Invitation_Model_Resource_Report_Invitation_Order_Collection
	 * @var string[]
	 */
	protected $_map = array('fields' => array(
		'invitee_email'	=> 'c.email'
		,'website_id' => 'w.website_id'
		,'invitation_email' => 'main_table.email'
		,'invitee_group_id' => 'main_table.group_id'
	));

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {$this->_itemObjectClass = Df_Invitation_Model_Invitation::class;}

}