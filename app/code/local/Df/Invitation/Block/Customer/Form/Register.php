<?php
class Df_Invitation_Block_Customer_Form_Register extends Mage_Customer_Block_Form_Register {
	/**
	 * Retrieve customer invitation
	 * @return Df_Invitation_Model_Invitation
	 */
	public function getCustomerInvitation() {return Mage::registry('current_invitation');}

	/**
	 * Retrieve form data
	 * @return Varien_Object
	 */
	public function getFormData()
	{
		$data = $this->_getData('form_data');
		if (is_null($data)) {
			$customerFormData = rm_session_customer()->getCustomerFormData(true);
			$data = new Varien_Object($customerFormData);
			if (empty($customerFormData)) {
				$invitation = $this->getCustomerInvitation();
				if ($invitation->getId()) {
					// check, set invitation email
					$data->setEmail($invitation->getEmail());
				}
			}
			$this->setData('form_data', $data);
		}
		return $data;
	}

	/**
	 * Retrieve form posting url
	 * @return string
	 */
	public function getPostActionUrl() {return $this->getUrl('*/*/createpost', array('_current'=>true));}
}