<?php
class Df_Invitation_Block_Adminhtml_Invitation_View_Tab_General
	extends Df_Core_Block_Admin
	implements Mage_Adminhtml_Block_Widget_Tab_Interface {
	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->setTemplate('df/invitation/view/tab/general.phtml');
	}

	/**
	 * Tab label getter
	 * @return string
	 */
	public function getTabLabel()
	{
		return df_h()->invitation()->__('General');
	}

	/**
	 * Tab Title getter
	 * @return string
	 */
	public function getTabTitle()
	{
		return $this->getTabLabel();
	}

	/**
	 * Check whether tab can be showed
	 * @return bool
	 */
	public function canShowTab()
	{
		return true;
	}

	/**
	 * Check whether tab is hidden
	 * @return bool
	 */
	public function isHidden()
	{
		return false;
	}

	/**
	 * Return Invitation for view
	 * @return Df_Invitation_Model_Invitation
	 */
	public function getInvitation()
	{
		return Mage::registry('current_invitation');
	}

	/**
	 * Check whether it is possible to edit invitation message
	 * @return bool
	 */
	public function canEditMessage()
	{
		return $this->getInvitation()->canMessageBeUpdated();
	}

	/**
	 * Return save message button html
	 * @return string
	 */
	public function getSaveMessageButtonHtml()
	{
		return $this->getChildHtml('save_message_button');
	}

	/**
	 * Retrieve formating date
	 *
	 * @param  string $date
	 * @param  string $format
	 * @param  bool $showTime
	 * @return  string
	 */
	public function formatDate($date=null, $format='short', $showTime=false)
	{
		if (is_string($date)) {
			$date = Mage::app()->getLocale()->date($date, Varien_Date::DATETIME_INTERNAL_FORMAT);
		}
		return parent::formatDate($date, $format, $showTime);
	}

	/**
	 * Return invintation customer model
	 * @return Df_Customer_Model_Customer|bool
	 */
	public function getReferral()
	{
		if (!$this->hasData('referral')) {
			if ($this->getInvitation()->getReferralId()) {
				$referral = Df_Customer_Model_Customer::ld(
					$this->getInvitation()->getReferralId()
				);
			} else {
				$referral = false;
			}
			$this->setData('referral', $referral);
		}
		return $this->_getData('referral');
	}

	/**
	 * Return invitation customer model
	 * @return Df_Customer_Model_Customer
	 */
	public function getCustomer()
	{
		if (!$this->hasData('customer')) {
			if ($this->getInvitation()->getCustomerId()) {
				$customer = Df_Customer_Model_Customer::ld(
					$this->getInvitation()->getCustomerId()
				);
			} else {
				$customer = false;
			}

			$this->setData('customer', $customer);
		}
		return $this->_getData('customer');
	}

	/**
	 * Return customer group collection
	 * @return Mage_Customer_Model_Entity_Group_Collection
	 */
	public function getCustomerGroupCollection()
	{
		if (!$this->hasData('customer_groups_collection')) {
			/** @var Mage_Customer_Model_Resource_Group_Collection $groups */
			$groups = df_model('customer/group')->getCollection();
			$groups
				->addFieldToFilter('customer_group_id', array('gt'=> 0))
				->load()
			;
			$this->setData('customer_groups_collection', $groups);
		}
		return $this->_getData('customer_groups_collection');
	}

	/**
	 * Return customer group code by group id
	 * If $configUsed passed as true then result will be default string
	 * instead of N/A sign
	 *
	 * @param int $groupId
	 * @param bool $configUsed
	 * @return string
	 */
	public function getCustomerGroupCode($groupId, $configUsed = false)
	{
		$group = $this->getCustomerGroupCollection()->getItemById($groupId);
		if ($group) {
			return $group->getCustomerGroupCode();
		} else {
			if ($configUsed) {
				return df_h()->invitation()->__('Default from System Configuration');
			} else {
				return df_h()->invitation()->__('N/A');
			}
		}
	}

	/**
	 * Invitation website name getter
	 * @return string
	 */
	public function getWebsiteName()
	{
		return Mage::app()->getStore($this->getInvitation()->getStoreId())
			->getWebsite()->getName();
	}

	/**
	 * Invitation store name getter
	 * @return string
	 */
	public function getStoreName()
	{
		return Mage::app()->getStore($this->getInvitation()->getStoreId())
			->getName();
	}

	/**
	 * Get invitation URL in case if it can be accepted
	 * @return string|bool
	 */
	public function getInvitationUrl()
	{
		if (!$this->getInvitation()->canBeAccepted(
			Mage::app()->getStore($this->getInvitation()->getStoreId())->getWebsiteId())) {
			return false;
		}
		return df_h()->invitation()->getInvitationUrl($this->getInvitation());
	}

	/**
	 * Checks if this invitation was sent by admin
	 * @return boolean - true if this invitation was sent by admin, false otherwise
	 */
	public function isInvitedByAdmin()
	{
		$invitedByAdmin = ($this->getInvitation()->getCustomerId() == null);
		return $invitedByAdmin;
	}

	/**
	 * Check whether can show referral link
	 * @return bool
	 */
	public function canShowReferralLink()
	{
		return df_mage()->admin()->session()->isAllowed('customer/manage');
	}

	/** @return Df_Invitation_Block_Adminhtml_Invitation_View_Tab_General */
	public static function i() {return df_block(__CLASS__);}
}