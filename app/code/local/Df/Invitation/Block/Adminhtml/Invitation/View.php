<?php
class Df_Invitation_Block_Adminhtml_Invitation_View extends Mage_Adminhtml_Block_Widget_Container {
	/**
	 * Set header text, add some buttons
	 * @return Df_Invitation_Block_Adminhtml_Invitation_View
	 */
	protected function _prepareLayout()
	{
		$invitation = $this->getInvitation();
		$this->_headerText = df_h()->invitation()->__('View Invitation for %s (ID: %s)',$invitation->getEmail(), $invitation->getId()
		);
		$this->_addButton('back', array(
			'label' => df_h()->invitation()->__('Back')
			,'onclick' => df_admin_button_location('*/*/')
			,'class' => 'back'
		), -1);
		if ($invitation->canBeCanceled()) {
			$massCancelUrl = $this->getUrl('*/*/massCancel', array('_query' => array('invitations' => array($invitation->getId()))));
			$this
				->_addButton(
					'cancel'
					,array(
						'label' => df_h()->invitation()->__('Discard Invitation')
						,'onclick' =>
							df_sprintf(
								'deleteConfirm(%s, %s)'
								,df_quote_single(
									df_h()->invitation()->__(
										'Are you sure you want to discard this invitation?'
									)
								)
								,df_quote_single($massCancelUrl)
							)
						,'class' => 'cancel'
					)
					, -1
				)
			;
		}
		if ($invitation->canMessageBeUpdated()) {
			$this
				->_addButton(
					'save_message_button'
					,array(
						'label' => df_h()->invitation()->__('Save Invitation')
						,'onclick' => 'invitationForm.submit()'
					)
					, -1
				)
			;
		}
		if ($invitation->canBeSent()) {
			$massResendUrl = $this->getUrl('*/*/massResend', array('_query' => http_build_query(array('invitations' => array($invitation->getId())))));
			$this->_addButton('resend', array(
				'label' => df_h()->invitation()->__('Send Invitation')
				,'onclick' => df_admin_button_location($massResendUrl)
			), -1);
		}
		parent::_prepareLayout();
		return $this;
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
	 * Retrieve save message url
	 * @return string
	 */
	public function getSaveMessageUrl()
	{
		return $this->getUrl('*/*/saveInvitation', array('id'=>$this->getInvitation()->getId()));
	}
}