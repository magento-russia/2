<?php
/**
 * Reward customer controller
 */
class Df_Reward_CustomerController extends Mage_Core_Controller_Front_Action {
	/**
	 * Predispatch
	 * Check is customer authenticate
	 * Check is RP enabled on frontend
	 */
	public function preDispatch()
	{
		parent::preDispatch();
		if (!rm_session_customer()->authenticate($this)) {
			$this->setFlag('', self::FLAG_NO_DISPATCH, true);
		}
		if (!df_h()->reward()->isEnabledOnFront()
			|| !df_h()->reward()->getHasRates()) {
			$this->norouteAction();
			$this->setFlag('', self::FLAG_NO_DISPATCH, true);
		}
	}

	/**
	 * Info Action
	 */
	public function infoAction()
	{
		Mage::register('current_reward', $this->_getReward());
		$this->loadLayout();
		$this->_initLayoutMessages('customer/session');
		$this->renderLayout();
	}

	/**
	 * Save settings
	 */
	public function saveSettingsAction()
	{
		if (!$this->_validateFormKey()) {
			$this->_redirect('*/*/info');
		}

		$customer = $this->_getCustomer();
		if ($customer->getId()) {
			$customer->setRewardUpdateNotification($this->getRequest()->getParam('subscribe_updates'))
				->setRewardWarningNotification($this->getRequest()->getParam('subscribe_warnings'));
			$customer->getResource()->saveAttribute($customer, 'reward_update_notification');
			$customer->getResource()->saveAttribute($customer, 'reward_warning_notification');
			$this->_getSession()->addSuccess(
				$this->__('Settings were successfully saved.')
			);
		}
		$this->_redirect('*/*/info');
	}

	/**
	 * Unsubscribe customer from update/warning balance notifications
	 */
	public function unsubscribeAction()
	{
		$notification = $this->getRequest()->getParam('notification');
		if (!in_array($notification, array('update','warning'))) {
			$this->_forward('noroute');
		}

		try {
			/* @var Df_Customer_Model_Customer $customer */
			$customer = $this->_getCustomer();
			if ($customer->getId()) {
				if ($notification == 'update') {
					$customer->setRewardUpdateNotification(false);
					$customer->getResource()->saveAttribute($customer, 'reward_update_notification');
				} else if ($notification == 'warning') {
					$customer->setRewardWarningNotification(false);
					$customer->getResource()->saveAttribute($customer, 'reward_warning_notification');
				}
				$this->_getSession()->addSuccess(
					$this->__('You have been successfully unsubscribed.')
				);
			}
		} catch (Exception $e) {
			$this->_getSession()->addError($this->__('Unsubscribtion failed.'));
		}
		$this->_redirect('*/*/info');
	}

	/** @return Mage_Customer_Model_Session */
	protected function _getSession() {return rm_session_customer();}

	/** @return Df_Customer_Model_Customer */
	protected function _getCustomer() {return $this->_getSession()->getCustomer();}

	/** @return Df_Reward_Model_Reward */
	protected function _getReward() {
		return Df_Reward_Model_Reward::i()
			->setCustomer($this->_getCustomer())
			->setWebsiteId(rm_website_id())
			->loadByCustomer()
		;
	}
}