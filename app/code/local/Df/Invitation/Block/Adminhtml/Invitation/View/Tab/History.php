<?php
class Df_Invitation_Block_Adminhtml_Invitation_View_Tab_History
	extends Df_Core_Block_Admin
	implements Mage_Adminhtml_Block_Widget_Tab_Interface {
	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->setTemplate('df/invitation/view/tab/history.phtml');
	}

	public function getTabLabel()
	{
		return df_h()->invitation()->__('Status History');
	}
	public function getTabTitle()
	{
		return df_h()->invitation()->__('Status History');
	}

	public function canShowTab()
	{
		return true;
	}
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
	 * Return invintation status history collection
	 * @return Df_Invitation_Model_Resource_Invitation_History_Collection
	 */
	public function getHistoryCollection()
	{
		return
			Df_Invitation_Model_Invitation_History::c()
				->addFieldToFilter('invitation_id', $this->getInvitation()->getId())
				->addOrder('history_id')
		;
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
	 * Retrieve formating time
	 *
	 * @param  string $date
	 * @param  string $format
	 * @param  bool $showDate
	 * @return  string
	 */
	public function formatTime($date=null, $format='short', $showDate=false)
	{
		if (is_string($date)) {
			$date = Mage::app()->getLocale()->date($date, Varien_Date::DATETIME_INTERNAL_FORMAT);
		}
		return parent::formatTime($date, $format, $showDate);
	}

	/** @return Df_Invitation_Block_Adminhtml_Invitation_View_Tab_History */
	public static function i() {return df_block(__CLASS__);}
}