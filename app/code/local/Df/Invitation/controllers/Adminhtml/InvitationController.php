<?php
class Df_Invitation_Adminhtml_InvitationController extends Mage_Adminhtml_Controller_Action {
	/**
	 * Invitation list
	 * @return void
	 */
	public function indexAction()
	{
		$this->_title($this->__('Customers'))->_title($this->__('Invitations'));
		$this->loadLayout()->_setActiveMenu('customer/invitation');
		$this->renderLayout();
	}

	/**
	 * Init invitation model by request
	 * @return Df_Invitation_Model_Invitation
	 */
	protected function _initInvitation()
	{
		$this->_title($this->__('Customers'))->_title($this->__('Invitations'));
		/** @var Df_Invitation_Model_Invitation $invitation */
		$invitation = Df_Invitation_Model_Invitation::ld($this->getRequest()->getParam('id'));
		if (!$invitation->getId()) {
			Mage::throwException(df_h()->invitation()->__('Invitation not found.'));
		}
		Mage::register('current_invitation', $invitation);
		return $invitation;
	}

	/**
	 * Invitation view action
	 */
	public function viewAction()
	{
		try {
			$this->_initInvitation();
			$this->loadLayout()->_setActiveMenu('customer/invitation');
			$this->renderLayout();
		}
		catch (Mage_Core_Exception $e) {
			rm_exception_to_session($e);
			$this->_redirect('*/*/');
		}
	}

	/**
	 * Create new invitatoin form
	 */
	public function newAction()
	{
		$this->loadLayout()->_setActiveMenu('df_invitation');
		$this->renderLayout();
	}

	/**
	 * Create & send new invitations
	 */
	public function saveAction()
	{
		try {
			// parse POST data
			if (!$this->getRequest()->isPost()) {
				$this->_redirect('*/*/');
				return;
			}
			$this->_getSession()->setInvitationFormData($this->getRequest()->getPost());
			$emails = preg_split('/\s+/s', $this->getRequest()->getParam('email'));
			foreach ($emails as $key => $email) {
				$email = trim($email);
				if (empty($email)) {
					unset($emails[$key]);
				}
				else {
					$emails[$key] = $email;
				}
			}
			if (empty($emails)) {
				Mage::throwException(df_h()->invitation()->__('Specify at least one email.'));
			}
			if (Mage::app()->isSingleStoreMode()) {
				$storeId = df_store(true)->getId();
			}
			else {
				$storeId = $this->getRequest()->getParam('store_id');
			}

			// try to send invitation(s)
			$sentCount   = 0;
			$failedCount = 0;
			$customerExistsCount = 0;
			foreach ($emails as $key => $email) {
				try {
					/** @var Df_Invitation_Model_Invitation $invitation */
					$invitation =
						Df_Invitation_Model_Invitation::i(
							array(
								'email'	=> $email
								,'store_id' => $storeId
								,'message' => $this->getRequest()->getParam('message')
								,'group_id' => $this->getRequest()->getParam('group_id')
							)
						)
					;
					$invitation->save();
					if ($invitation->sendInvitationEmail()) {
						$sentCount++;
					}
					else {
						$failedCount++;
					}
				}
				catch (Mage_Core_Exception $e) {
					if ($e->getCode()) {
						$failedCount++;
						if ($e->getCode() == Df_Invitation_Model_Invitation::ERROR_CUSTOMER_EXISTS) {
							$customerExistsCount++;
						}
					}
					else {
						df_error($e);
					}
				}
			}
			if ($sentCount) {
				df_session()->addSuccess(df_h()->invitation()->__('%d invitation(s) were sent.', $sentCount));
			}
			if ($failedCount) {
				df_session()->addError(df_h()->invitation()->__('Failed to send %1$d of %2$d invitation(s).', $failedCount, count($emails)));
			}
			if ($customerExistsCount) {
				df_session()->addNotice(df_h()->invitation()->__('%d invitation(s) were not sent, because customer accounts already exist for specified email addresses.', $customerExistsCount));
			}
			$this->_getSession()->unsInvitationFormData();
			$this->_redirect('*/*/');
			return;
		}
		catch (Mage_Core_Exception $e) {
			rm_exception_to_session($e);
		}
		$this->_redirect('*/*/new');
	}

	/**
	 * Edit invitation's information
	 * @return void
	 */
	public function saveInvitationAction()
	{
		try {
			$invitation = $this->_initInvitation();
			if ($this->getRequest()->isPost()) {
				$email = $this->getRequest()->getParam('email');
				$invitation->setMessage($this->getRequest()->getParam('message'))
					->setEmail($email);
				$result = $invitation->validate();
				//checking if there was validation
				if (is_array($result) && !empty($result)) {
					foreach ($result as $message) {
						df_session()->addError($message);
					}
					$this->_redirect('*/*/view', array('_current' => true));
					return;
				}

				//If there was no validation errors trying to save
				$invitation->save();
				df_session()->addSuccess(df_h()->invitation()->__('Invitation was successfully saved.'));
			}
		}
		catch (Mage_Core_Exception $e) {
			rm_exception_to_session($e);
		}
		$this->_redirect('*/*/view', array('_current' => true));
	}

	/**
	 * Action for mass-resending invitations
	 */
	public function massResendAction()
	{
		try {
			$invitationsPost = $this->getRequest()->getParam('invitations', array());
			if (empty($invitationsPost) || !is_array($invitationsPost)) {
				Mage::throwException(df_h()->invitation()->__('Please select invitations.'));
			}
			/** @var Df_Invitation_Model_Resource_Invitation_Collection $collection */
			$collection = Df_Invitation_Model_Invitation::c();
			$collection->addFieldToFilter('invitation_id', array('in' => $invitationsPost));
			$collection->addCanBeSentFilter();
			$found = 0;
			$sent  = 0;
			$customerExists = 0;
			foreach ($collection as $invitation) {
				try {
					$invitation->makeSureCanBeSent();
					$found++;
					if ($invitation->sendInvitationEmail()) {
						$sent++;
					}
				}
				catch (Mage_Core_Exception $e) {
					// jam all exceptions with codes
					if (!$e->getCode()) {
						df_error($e);
					}
					// close irrelevant invitations
					if ($e->getCode() === Df_Invitation_Model_Invitation::ERROR_CUSTOMER_EXISTS) {
						$customerExists++;
						$invitation->cancel();
					}
				}
			}
			if (!$found) {
				df_session()->addError(df_h()->invitation()->__('No invitations have been resent'));
			}
			if ($sent) {
				df_session()->addSuccess(df_h()->invitation()->__('%1$d of %2$d invitations were sent.', $sent, $found));
			}
			$failed = $found - $sent;
			if ($failed) {
				df_session()->addError(df_h()->invitation()->__('Failed to send %d invitation(s).', $failed));
			}
			if ($customerExists) {
				df_session()->addNotice(
					df_h()->invitation()->__('%d invitation(s) cannot be sent, because customer already exists for their emails. These invitations were discarded.', $customerExists)
				);
			}
		}
		catch (Mage_Core_Exception $e) {
			rm_exception_to_session($e);
		}
		$this->_redirect('*/*/');
	}

	/**
	 * Action for mass-cancelling invitations
	 */
	public function massCancelAction()
	{
		try {
			$invitationsPost = $this->getRequest()->getParam('invitations', array());
			if (empty($invitationsPost) || !is_array($invitationsPost)) {
				Mage::throwException(df_h()->invitation()->__('Please select invitations.'));
			}
			/** @var Df_Invitation_Model_Resource_Invitation_Collection $collection */
			$collection = Df_Invitation_Model_Invitation::c();
			$collection->addFieldToFilter('invitation_id', array('in' => $invitationsPost));
			$collection->addCanBeCanceledFilter();
			$found = 0;
			$cancelled = 0;
			foreach ($collection as $invitation) {
				try {
					$found++;
					if ($invitation->canBeCanceled()) {
						$invitation->cancel();
						$cancelled++;
					}
				}
				catch (Mage_Core_Exception $e) {
					// jam all exceptions with codes
					if (!$e->getCode()) {
						df_error($e);
					}
				}
			}
			if ($cancelled) {
				df_session()->addSuccess(df_h()->invitation()->__('%1$d of %2$d invitations were discarded.', $cancelled, $found));
			}
			$failed = $found - $cancelled;
			if ($failed) {
				df_session()->addNotice(df_h()->invitation()->__('%d of selected invitation(s) were skipped.', $failed));
			}
		}
		catch (Mage_Core_Exception $e) {
			rm_exception_to_session($e);
		}
		$this->_redirect('*/*/');
	}

	/**
	 * Acl admin user check
	 * @return boolean
	 */
	protected function _isAllowed() {
		return df_h()->invitation()->config()->isEnabled() && rm_admin_allowed('customer/df_invitation');
	}
}