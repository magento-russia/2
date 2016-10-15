<?php
/**
 * Reward admin rate controller
 */
class Df_Reward_Adminhtml_Reward_RateController extends Mage_Adminhtml_Controller_Action {
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
	 * Initialize layout, breadcrumbs
	 * @return Df_Reward_Adminhtml_Reward_RateController
	 */
	protected function _initAction()
	{
		$this->loadLayout()
			->_setActiveMenu('customer/reward_rates')
			->_addBreadcrumb(df_h()->reward()->__('Customers'),df_h()->reward()->__('Customers'))
			->_addBreadcrumb(df_h()->reward()->__('Manage Reward Exchange Rates'),df_h()->reward()->__('Manage Reward Exchange Rates'));
		return $this;
	}

	/**
	 * Initialize rate object
	 * @return Df_Reward_Model_Reward_Rate
	 */
	protected function _initRate()
	{
		$this->_title($this->__('Customers'))->_title($this->__('Reward Exchange Rates'));
		$rateId = $this->getRequest()->getParam('rate_id', 0);
		$rate = Df_Reward_Model_Reward_Rate::i();
		if ($rateId) {
			$rate->load($rateId);
		}
		Mage::register('current_reward_rate', $rate);
		return $rate;
	}

	/**
	 * Index Action
	 */
	public function indexAction()
	{
		$this->_title($this->__('Customers'))->_title($this->__('Reward Exchange Rates'));
		$this->_initAction()
			->renderLayout();
	}

	/**
	 * New Action.
	 * Forward to Edit Action
	 */
	public function newAction()
	{
		$this->_forward('edit');
	}

	/**
	 * Edit Action
	 */
	public function editAction()
	{
		$rate = $this->_initRate();
		$this->_title($rate->getRateId() ? df_sprintf("#%s", $rate->getRateId()) : $this->__('New Rate'));
		$this->_initAction()
			->renderLayout();
	}

	/**
	 * Save Action
	 */
	public function saveAction()
	{
		$data = $this->getRequest()->getPost('rate');
		if ($data) {
			$rate = $this->_initRate();
			if ($this->getRequest()->getParam('rate_id') && ! $rate->getId()) {
				return $this->_redirect('*/*/');
			}

			$rate->addData($data);
			try {
				$rate->save();
				df_session()->addSuccess(df_h()->reward()->__('Rate saved successfully.'));
			} catch (Exception $e) {
				df_handle_entry_point_exception($e, false);
				df_session()->addError($this->__('Cannot save Rate.'));
				return $this->_redirect('*/*/edit', array('rate_id' => $rate->getId(), '_current' => true));
			}
		}
		return $this->_redirect('*/*/');
	}

	/**
	 * Delete Action
	 */
	public function deleteAction()
	{
		$rate = $this->_initRate();
		if ($rate->getId()) {
			try {
				$rate->delete();
				df_session()->addSuccess(df_h()->reward()->__('Rate deleted successfully.'));
			} catch (Exception $e) {
				df_exception_to_session($e);
				$this->_redirect('*/*/*', array('_current' => true));
				return;
			}
		}
		$this->_redirect('*/*/');
	}

	/**
	 * Validate Action
	 *
	 */
	public function validateAction()
	{
		$response = new Varien_Object(array('error' => false));
		$post	 = $this->getRequest()->getParam('rate');
		$message  = null;
		if (!isset($post['customer_group_id'])
			|| !isset($post['website_id'])
			|| !isset($post['direction'])
			|| !isset($post['value'])
			|| !isset($post['equal_value'])) {
			$message = $this->__('Please enter all Rate information.');
		} else if ($post['direction'] == Df_Reward_Model_Reward_Rate::RATE_EXCHANGE_DIRECTION_TO_CURRENCY
				  && ((int) $post['value'] <= 0 || (float) $post['equal_value'] <= 0)) {
			  if ((int) $post['value'] <= 0) {
				  $message = $this->__('Please enter positive integer number in left Rate field');
			  } else {
				  $message = $this->__('Please enter positive number in right Rate field');
			  }
		} else if ($post['direction'] == Df_Reward_Model_Reward_Rate::RATE_EXCHANGE_DIRECTION_TO_POINTS
				  && ((float) $post['value'] <= 0 || (int) $post['equal_value'] <= 0)) {
			  if ((int) $post['equal_value'] <= 0) {
				  $message = $this->__('Please enter positive integer number in right Rate field');
			  } else {
				  $message = $this->__('Please enter positive number in left Rate field');
			  }
		} else {
			$rate	   = $this->_initRate();
			$isRateUnique = $rate->getIsRateUniqueToCurrent($post['website_id'], $post['customer_group_id'], $post['direction']);
			if (!$isRateUnique) {
				$message = $this->__('Rate with same Website, Custormer Group and Direction or covering Rate already exists.');
			}
		}

		if ($message) {
			df_session()->addError($message);
			$this->_initLayoutMessages('adminhtml/session');
			$response->setError(true);
			$response->setMessage($this->getLayout()->getMessagesBlock()->getGroupedHtml());
		}

		$this->getResponse()->setBody($response->toJson());
	}

	/**
	 * Acl check for admin
	 * @return boolean
	 */
	protected function _isAllowed() {return rm_admin_allowed('df_reward/rates');}
}