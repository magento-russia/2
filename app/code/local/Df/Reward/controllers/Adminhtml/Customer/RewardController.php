<?php
/**
 * Reward admin customer controller
 */
class Df_Reward_Adminhtml_Customer_RewardController extends Mage_Adminhtml_Controller_Action {
	/**
	 * Check if module functionality enabled
	 * @return Df_Reward_Adminhtml_Reward_RateController
	 */
	public function preDispatch()
	{
		parent::preDispatch();
		if (!df_h()->reward()->isEnabled() && $this->getRequest()->getActionName() != 'noroute') {
			$this->_forward('noroute');
		}
		return $this;
	}

	/**
	 * History Ajax Action
	 */
	public function historyAction() {
		$this->getResponse()->setBody(
			Df_Reward_Block_Adminhtml_Customer_Edit_Tab_Reward_History::i(rm_request('id', 0))->toHtml()
		);
	}

	/** @return void */
	public function historyGridAction() {
		$this->getResponse()->setBody(
			Df_Reward_Block_Adminhtml_Customer_Edit_Tab_Reward_History_Grid::i(rm_request('id', 0))->toHtml()
		);
	}

	/**
	 *  Delete orphan points Action
	 */
	public function deleteOrphanPointsAction()
	{
		$customerId = $this->getRequest()->getParam('id', 0);
		if ($customerId) {
			try {
				Df_Reward_Model_Reward::i()->deleteOrphanPointsByCustomer($customerId);
				df_session()->addSuccess(df_h()->reward()->__('Orphan points removed successfully.'));
			} catch (Exception $e) {
				rm_exception_to_session($e);
			}
		}
		$this->_redirect('*/customer/edit', array('_current' => true));
	}

	/**
	 * Acl check for admin
	 * @return boolean
	 */
	protected function _isAllowed() {return rm_admin_allowed('df_reward/balance');}
}