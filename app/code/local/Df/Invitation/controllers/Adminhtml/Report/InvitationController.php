<?php
class Df_Invitation_Adminhtml_Report_InvitationController
	extends Mage_Adminhtml_Controller_Action {
	/**
	 * Init action breadcrumbs
	 * @return Df_Invitation_Adminhtml_Report_InvitationController
	 */
	public function _initAction()
	{
		$this->loadLayout()
			->_addBreadcrumb(Mage::helper('reports')->__('Reports'), Mage::helper('reports')->__('Reports'))
			->_addBreadcrumb(df_h()->invitation()->__('Invitations'), df_h()->invitation()->__('Invitations'));
		return $this;
	}

	/**
	 * General report action
	 */
	public function indexAction()
	{
		$this->_title($this->__('Reports'))
			 ->_title($this->__('Invitations'))
			 ->_title($this->__('General'));
		$this->_initAction()
			->_setActiveMenu('report/df_invitation/general')
			->_addBreadcrumb(
				df_h()->invitation()->__('General Report')
				, df_h()->invitation()->__('General Report')
			)
			->_addContent(Df_Invitation_Block_Adminhtml_Report_Invitation_General::i())
			->renderLayout();
	}

	/**
	 * Export invitation general report grid to CSV format
	 */
	public function exportCsvAction() {
		$this->_prepareDownloadResponse(
			'invitation_general.csv'
			, Df_Invitation_Block_Adminhtml_Report_Invitation_General_Grid::i()->getCsv()
		);
	}

	/**
	 * Export invitation general report grid to Excel XML format
	 */
	public function exportExcelAction() {
		$fileName = 'invitation_general.xml';
		$this->_prepareDownloadResponse(
			$fileName
			, Df_Invitation_Block_Adminhtml_Report_Invitation_General_Grid::i()->getExcel($fileName)
		);
	}

	/**
	 * Report by customers action
	 */
	public function customerAction()
	{
		$this->_title($this->__('Reports'))
			 ->_title($this->__('Invitations'))
			 ->_title($this->__('Customers'));
		$this->_initAction()
			->_setActiveMenu('report/df_invitation/customer')
			->_addBreadcrumb(df_h()->invitation()->__('Invitation Report by Customers'), df_h()->invitation()->__('Invitation Report by Customers'))
			->_addContent(Df_Invitation_Block_Adminhtml_Report_Invitation_Customer::i())
			->renderLayout();
	}

	/**
	 * Export invitation customer report grid to CSV format
	 */
	public function exportCustomerCsvAction()
	{
		$fileName = 'invitation_customer.csv';
		$this->_prepareDownloadResponse(
			$fileName
			, Df_Invitation_Block_Adminhtml_Report_Invitation_Customer_Grid::i()->getCsv()
		);
	}

	/**
	 * Export invitation customer report grid to Excel XML format
	 */
	public function exportCustomerExcelAction() {
		$fileName = 'invitation_customer.xml';
		$this->_prepareDownloadResponse(
			$fileName
			, Df_Invitation_Block_Adminhtml_Report_Invitation_Customer_Grid::i()->getExcel($fileName)
		);
	}

	/**
	 * Report by order action
	 */
	public function orderAction()
	{
		$this->_title($this->__('Reports'))
			 ->_title($this->__('Invitations'))
			 ->_title($this->__('Order Conversion Rate'));
		$this->_initAction()
			->_setActiveMenu('report/df_invitation/order')
			->_addBreadcrumb(df_h()->invitation()->__('Invitation Report by Customers'), df_h()->invitation()->__('Invitation Report by Order Conversion Rate'))
			->_addContent(Df_Invitation_Block_Adminhtml_Report_Invitation_Order::i())
			->renderLayout();
	}

	/**
	 * Export invitation order report grid to CSV format
	 */
	public function exportOrderCsvAction()
	{
		$this->_prepareDownloadResponse(
			'invitation_order.csv'
			, Df_Invitation_Block_Adminhtml_Report_Invitation_Order_Grid::i()->getCsv()
		);
	}

	/**
	 * Export invitation order report grid to Excel XML format
	 */
	public function exportOrderExcelAction() {
		$fileName = 'invitation_order.xml';
		$this->_prepareDownloadResponse(
			$fileName
			, Df_Invitation_Block_Adminhtml_Report_Invitation_Order_Grid::i()->getExcel($fileName)
		);
	}

	/**
	 * Acl admin user check
	 * @return boolean
	 */
	protected function _isAllowed() {
		return df_h()->invitation()->config()->isEnabled() && rm_admin_allowed('report/df_invitation');
	}
}