<?php
class Df_Invitation_Block_Adminhtml_Invitation_Grid_Column_Invitee
	extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
	/**
	 * Render invitee email linked to its account edit page
	 *
	 * @param  Varien_Object $row
	 * @return  string
	 */
	protected function _getValue(Varien_Object $row)
	{
		if (!$row->getReferralId()) {
			return '';
		}
		/** @var Df_Customer_Model_Customer $customer */
		$customer = Df_Customer_Model_Customer::ld($row->getReferralId());
		return
			'<a href="'
			. rm_url_admin('*/customer/edit', array('id' => $row->getReferralId()))
			. '">'
			. df_text()->escapeHtml($customer->getName())
			. '</a>'
		;
	}

	/** @return Df_Invitation_Block_Adminhtml_Invitation_Grid_Column_Invitee */
	public static function i() {return df_block(__CLASS__);}
}