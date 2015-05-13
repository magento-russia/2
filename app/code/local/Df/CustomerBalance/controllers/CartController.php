<?php
/**
 * Customer balance controller for shopping cart
 *
 */
class Df_CustomerBalance_CartController extends Mage_Core_Controller_Front_Action {
	/**
	 * Only logged in users can use this functionality, * this function checks if user is logged in before all other actions
	 *
	 */
	public function preDispatch()
	{
		parent::preDispatch();
		if (!rm_session_customer()->authenticate($this)) {
			$this->setFlag('', 'no-dispatch', true);
		}
	}

	/**
	 * Remove Store Credit from current quote
	 *
	 */
	public function removeAction()
	{
		if (!df_h()->customer()->balance()->isEnabled()) {
			$this->_redirect('customer/account/');
			return;
		}

		$quote = rm_session_checkout()->getQuote();
		if ($quote->getUseCustomerBalance()) {
			rm_session_checkout()->addSuccess(
				$this->__('Store Credit payment was successfully removed from your shopping cart.')
			);
			$quote->setUseCustomerBalance(false)->collectTotals()->save();
		} else {
			rm_session_checkout()->addError(
				$this->__('Store Credit payment is not being used in your shopping cart.')
			);
		}
		$this->_redirect('checkout/cart');
	}
}