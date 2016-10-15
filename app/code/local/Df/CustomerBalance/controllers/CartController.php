<?php
class Df_CustomerBalance_CartController extends Mage_Core_Controller_Front_Action {
	/** @return void */
	public function preDispatch() {
		parent::preDispatch();
		if (!df_session_customer()->authenticate($this)) {
			$this->setFlag('', 'no-dispatch', true);
		}
	}

	/** @return void */
	public function removeAction() {
		if (!Df_CustomerBalance_Helper_Data::s()->isEnabled()) {
			$this->_redirect('customer/account/');
		}
		else {
			if (df_quote()->getUseCustomerBalance()) {
				df_session_checkout()->addSuccess(
					$this->__('Store Credit payment was successfully removed from your shopping cart.')
				);
				df_quote()->setUseCustomerBalance(false)->collectTotals()->save();
			} else {
				df_session_checkout()->addError(
					$this->__('Store Credit payment is not being used in your shopping cart.')
				);
			}
			$this->_redirect('checkout/cart');
		}
	}
}