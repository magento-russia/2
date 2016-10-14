<?php
/**
 * Controller for Customer account -> Store Credit ajax tab and all its contents
 *
 */
class Df_CustomerBalance_Adminhtml_CustomerbalanceController extends Mage_Adminhtml_Controller_Action {
	/**
	 * Check is enabled module in config
	 * @return Df_CatalogEvent_Adminhtml_Catalog_EventController
	 */
	public function preDispatch()
	{
		parent::preDispatch();
		if (!Df_CustomerBalance_Helper_Data::s()->isEnabled()) {
			if ($this->getRequest()->getActionName() != 'noroute') {
				$this->_forward('noroute');
			}
		}
		return $this;
	}

	/**
	 * Customer balance form
	 *
	 */
	public function formAction()
	{
		$this->_initCustomer();
		$this->loadLayout();
		$this->renderLayout();
	}

	/**
	 * Customer balance grid
	 *
	 */
	public function gridHistoryAction()
	{
		$this->_initCustomer();
		$this->loadLayout();
		$this->getResponse()->setBody(rm_render(
			new Df_CustomerBalance_Block_Adminhtml_Customer_Edit_Tab_Customerbalance_Balance_History_Grid
		));
	}

	/**
	 * Delete orphan balances
	 *
	 */
	public function deleteOrphanBalancesAction()
	{
		$balance = Df_CustomerBalance_Model_Balance::s()->deleteBalancesByCustomerId(
			(int)$this->getRequest()->getParam('id')
		);
		$this->_redirect('*/customer/edit/', array('_current'=>true));
	}

	/**
	 * Instantiate customer model
	 *
	 * @param string $idFieldName
	 */
	protected function _initCustomer($idFieldName = 'id') {
		/** @var Df_Customer_Model_Customer $customer */
		$customer = Df_Customer_Model_Customer::ld((int)$this->getRequest()->getParam($idFieldName));
		if (!$customer->getId()) {
			Mage::throwException(Df_CustomerBalance_Helper_Data::s()->__('Failed to initialize customer'));
		}
		Mage::register('current_customer', $customer);
	}

	/**
	 * Check is allowed customer management
	 * @return bool
	 */
	protected function _isAllowed() {return rm_admin_allowed('customer/manage');}
}