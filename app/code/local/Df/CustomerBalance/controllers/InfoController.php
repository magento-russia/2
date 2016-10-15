<?php
/**
 * Customerbalance controller for My Account
 *
 */
class Df_CustomerBalance_InfoController extends Mage_Core_Controller_Front_Action {
	/**
	 * Only logged in users can use this functionality, * this function checks if user is logged in before all other actions
	 *
	 */
	public function preDispatch()
	{
		parent::preDispatch();
		if (!df_session_customer()->authenticate($this)) {
			$this->setFlag('', 'no-dispatch', true);
		}
	}

	/**
	 * Store Credit dashboard
	 *
	 */
	public function indexAction()
	{
		if (!Df_CustomerBalance_Helper_Data::s()->isEnabled()) {
			$this->_redirect('customer/account/');
			return;
		}
		$this->loadLayout();
		$this->_initLayoutMessages('customer/session');
		$this->loadLayoutUpdates();
		$this->renderLayout();
	}
}