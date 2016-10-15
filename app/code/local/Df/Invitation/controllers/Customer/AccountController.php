<?php
require_once 'Mage/Customer/controllers/AccountController.php';
class Df_Invitation_Customer_AccountController extends Mage_Customer_AccountController {
	/**
	 * Action list where need check enabled cookie
	 *
	 * @var array
	 */
	protected $_cookieCheckActions = array('createPost');

	/**
	 * Predispatch custom logic
	 *
	 * Bypassing direct parent predispatch
	 * Allowing only specific actions
	 * Checking whether invitation functionality is enabled
	 * Checking whether registration is allowed at all
	 * No way to logged in customers
	 */
	public function preDispatch() {
		Mage_Core_Controller_Front_Action::preDispatch();
		if (0 === preg_match('/^(create|createpost)/i', $this->getRequest()->getActionName())) {
			$this->norouteAction();
			$this->setFlag('', self::FLAG_NO_DISPATCH, true);
		}
		else if (!df_h()->invitation()->config()->isEnabledOnFront()) {
			$this->norouteAction();
			$this->setFlag('', self::FLAG_NO_DISPATCH, true);
		}
		else if (rm_customer_logged_in()) {
			$this->_redirect('customer/account/');
			$this->setFlag('', self::FLAG_NO_DISPATCH, true);
		}
		return $this;
	}

	/**
	 * Hack real module name in order to make translations working correctly
	 * @return string
	 */
	protected function _getRealModuleName()
	{
		return 'Mage_Customer';
	}

	/**
	 * Initialize invitation from request
	 * @return Df_Invitation_Model_Invitation
	 */
	protected function _initInvitation()
	{
		if (!Mage::registry('current_invitation')) {
			/** @var Df_Invitation_Model_Invitation $invitation */
			$invitation = Df_Invitation_Model_Invitation::i();
			$invitation
				->loadByInvitationCode(df_mage()->coreHelper()->urlDecode($this->getRequest()->getParam('invitation', false)))
				->makeSureCanBeAccepted();
			Mage::register('current_invitation', $invitation);
		}
		return Mage::registry('current_invitation');
	}

	/**
	 * Customer register form page
	 */
	public function createAction()
	{
		try {
			$invitation = $this->_initInvitation();
			$this->loadLayout();
			$this->_initLayoutMessages('customer/session');
			$this->renderLayout();
			return;
		}
		catch (Mage_Core_Exception $e) {
			df_exception_to_session($e);
		}
		$this->_redirect('customer/account/login');
	}

	/**
	 * Create customer account action
	 * @return void
	 */
	public function createPostAction()
	{
		try {
			$invitation = $this->_initInvitation();
			/** @var Df_Customer_Model_Customer $customer */
			$customer = Df_Customer_Model_Customer::i();
			$customer->setId(null);
			$customer->setSkipConfirmationIfEmail($invitation->getEmail());
			Mage::register('current_customer', $customer);
			$groupId = $invitation->getGroupId();
			if ($groupId) {
				$customer->setGroupId($groupId);
			}
			parent::createPostAction();
			$customerId = $customer->getId();
			if ($customerId) {
				$invitation->accept(df_website_id(), $customerId);
				Mage::dispatchEvent('df_invitation_customer_accepted', array(
				   'customer' => $customer,   'invitation' => $invitation
				));
			}
			return;
		}
		catch (Mage_Core_Exception $e) {
			$_definedErrorCodes = array(
				Df_Invitation_Model_Invitation::ERROR_CUSTOMER_EXISTS,Df_Invitation_Model_Invitation::ERROR_INVALID_DATA
			);
			if (in_array($e->getCode(), $_definedErrorCodes)) {
				df_exception_to_session($e);
				df_session()->setCustomerFormData($this->getRequest()->getPost());
			} else {
				if (Mage::helper('customer')->isRegistrationAllowed()) {
					df_session()->addError(
						df_h()->invitation()->__('Your invitation is not valid. Please create an account.')
					);
					$this->_redirect('customer/account/create');
					return;
				} else {
					df_session()->addError(
						df_h()->invitation()->__('Your invitation is not valid. Please contact us at %s.', Mage::getStoreConfig('trans_email/ident_support/email'))
					);
					$this->_redirect('customer/account/login');
					return;
				}
			}
		}
		catch (Exception $e) {
			$this->_getSession()->setCustomerFormData($this->getRequest()->getPost())
				->addException($e, Mage::helper('customer')->__('Can\'t save customer'));
		}
		$this->_redirectError('');
	}

	/**
	 * Make success redirect constant
	 *
	 * @param string $defaultUrl
	 * @return Df_Invitation_Customer_AccountController
	 */
	protected function _redirectSuccess($defaultUrl)
	{
		return $this->_redirect('customer/account/');
	}

	/**
	 * Make failure redirect constant
	 *
	 * @param string $defaultUrl
	 * @return Df_Invitation_Customer_AccountController
	 */
	protected function _redirectError($defaultUrl)
	{
		return $this->_redirect('df_invitation/customer_account/create',array('_current' => true, '_secure' => true));
	}
}