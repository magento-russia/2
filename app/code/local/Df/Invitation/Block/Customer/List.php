<?php
class Df_Invitation_Block_Customer_List extends Mage_Customer_Block_Account_Dashboard {
	/**
	 * Return list of invitations
	 * @return Df_Invitation_Model_Resource_Invitation_Collection
	 */
	public function getInvitationCollection() {
		if (!$this->hasInvitationCollection()) {
			/** @var Df_Invitation_Model_Resource_Invitation_Collection $collection */
			$collection = Df_Invitation_Model_Invitation::c();
			$collection->addOrder('invitation_id', Varien_Data_Collection::SORT_ORDER_DESC);
			$collection->loadByCustomerId(rm_session_customer()->getCustomerId());
			$this
				->setData(
					'invitation_collection'
					,$collection
				)
			;
		}
		return $this->_getData('invitation_collection');
	}

	/**
	 * Return status text for invitation
	 *
	 * @param Df_Invitation_Model_Invitation $invitation
	 * @return string
	 */
	public function getStatusText($invitation)
	{
		return Df_Invitation_Model_Source_Invitation_Status::s()
			->getOptionText($invitation->getStatus());
	}
}