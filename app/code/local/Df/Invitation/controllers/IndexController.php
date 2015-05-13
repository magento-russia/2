<?php
class Df_Invitation_IndexController extends Mage_Core_Controller_Front_Action {
	/**
	 * Only logged in users can use this functionality, * this function checks if user is logged in before all other actions
	 *
	 */
	public function preDispatch()
	{
		parent::preDispatch();
		if (!df_h()->invitation()->config()->isEnabledOnFront()) {
			$this->norouteAction();
			$this->setFlag('', self::FLAG_NO_DISPATCH, true);
			return;
		}

		if (!rm_session_customer()->authenticate($this)) {
			$this->getResponse()->setRedirect(Mage::helper('customer')->getLoginUrl());
			$this->setFlag('', self::FLAG_NO_DISPATCH, true);
		}
	}

	/** @return void */
	public function sendAction() {
		/** @var mixed[]|null $data */
		$data = $this->getRequest()->getPost();
		if ($data) {
			$customer = rm_session_customer()->getCustomer();
			$invPerSend = df_h()->invitation()->config()->getMaxInvitationsPerSend();
			$attempts = 0;
			$sent	 = 0;
			$customerExists = 0;
			foreach ($data['email'] as $email) {
				$attempts++;
				if (!Zend_Validate::is($email, 'EmailAddress')) {
					continue;
				}
				if ($attempts > $invPerSend) {
					continue;
				}
				try {
					/** @var Df_Invitation_Model_Invitation $invitation */
					$invitation =
						Df_Invitation_Model_Invitation::i(
							array(
								'email'	=> $email
								,'customer' => $customer
								,'message'  => (isset($data['message']) ? $data['message'] : '')
							)
						)
					;
					$invitation->save();
					if ($invitation->sendInvitationEmail()) {
						rm_session_customer()
							->addSuccess(
								df_h()->invitation()->__(
									'Invitation for %s has been sent successfully.'
									,$email
								)
							)
						;
						$sent++;
					}
					else {
						throw new Exception(''); // not Mage_Core_Exception intentionally
					}

				}
				catch(Mage_Core_Exception $e) {
					if (Df_Invitation_Model_Invitation::ERROR_CUSTOMER_EXISTS === $e->getCode()) {
						$customerExists++;
					}
					else {
						rm_session_customer()->addError(rm_ets($e));
					}
				}
				catch(Exception $e) {
					rm_session_customer()->addError(df_h()->invitation()->__('Failed to send email to %s. Please try again later.', $email));
				}
			}
			if ($customerExists) {
				rm_session_customer()->addNotice(
					df_h()->invitation()->__('%d invitation(s) were not sent, because customer accounts already exist for specified email addresses.', $customerExists)
				);
			}
			$this->_redirect('*/*/');
			return;
		}

		$this->loadLayout();
		$this->_initLayoutMessages('customer/session');
		$this->loadLayoutUpdates();
		$this->renderLayout();
	}

	/**
	 * View invitation list in 'My Account' section
	 *
	 */
	public function indexAction()
	{
		$this->loadLayout();
		$this->_initLayoutMessages('customer/session');
		$this->loadLayoutUpdates();
		if ($block = $this->getLayout()->getBlock('invitations_list')) {
			$block->setRefererUrl($this->_getRefererUrl());
		}
		$this->renderLayout();
	}
}