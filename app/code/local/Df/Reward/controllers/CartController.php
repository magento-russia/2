<?php
class Df_Reward_CartController extends Mage_Core_Controller_Front_Action {
	/**
	 * Only logged in users can use this functionality, * this function checks if user is logged in before all other actions
	 *
	 */
	public function preDispatch() {
		parent::preDispatch();
		if (!df_session_customer()->authenticate($this)) {
			$this->setFlag('', self::FLAG_NO_DISPATCH, true);
		}
	}

	/**
	 * Remove Reward Points payment from current quote
	 *
	 */
	public function removeAction() {
		if (!df_h()->reward()->isEnabledOnFront() || !df_h()->reward()->getHasRates()) {
			$this->_redirect('customer/account/');
		}
		if (rm_quote()->getUseRewardPoints()) {
			rm_quote()->setUseRewardPoints(false)->collectTotals()->save();
			df_session_checkout()->addSuccess(
				$this->__('Reward Points were successfully removed from your order.')
			);
		} else {
			df_session_checkout()->addError(
				$this->__('Reward Points will not be used in this order.')
			);
		}
		$this->_redirect('checkout/cart');
	}
}