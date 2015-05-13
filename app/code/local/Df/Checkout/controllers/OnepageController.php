<?php
require_once 'Mage/Checkout/controllers/OnepageController.php';
class Df_Checkout_OnepageController extends Mage_Checkout_OnepageController {
	/**
	 * @override
	 * @return Df_Checkout_Model_Type_Onepage
	 */
	public function getOnepage() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Checkout_Model_Type_Onepage::i();
			$this->{__METHOD__}->setController($this);
		}
		return $this->{__METHOD__};
	}

	/**
	* Shipping method save action
	*/
	public function saveShippingMethodAction() {
		if ($this->_expireAjax()) {
			return;
		}
		if ($this->getRequest()->isPost()) {
			/** @var string $shippingMethodAsString */
			$shippingMethodAsString = df_nts($this->getRequest()->getPost('shipping_method'));
			$result = $this->getOnepage()->saveShippingMethod($shippingMethodAsString);
			/*
			$result will have erro data if shipping method is empty
			*/
			if (!$result) {
				Mage::dispatchEvent(
					'checkout_controller_onepage_save_shipping_method'
					,array(
						'request'=>$this->getRequest()
						,'quote'=>$this->getOnepage()->getQuote()
					)
				);
				$this->getOnepage()->getQuote()->collectTotals();
				$this->getResponse()->setBody(df_mage()->coreHelper()->jsonEncode($result));
				$result['goto_section'] = 'payment';
				$result['update_section'] = array(
					'name' => 'payment-method','html' => $this->_getPaymentMethodsHtml()
				);
				/**
				 * BEGIN PATCH
				 */
				Mage::app()->getCacheInstance()->banUse('layout');
				/** @var Exception $exception|null */
				$exception = null;
				try {
					$this->loadLayout('checkout_onepage_review');
					$result['df_update_sections'] =
						array(
							array(
								'name' => 'review','html' =>
									$this->getLayout()->getBlock('root')->toHtml()
							)
						)
					;
				}
				catch(Exception $e) {
					$exception = $e;
				}
				if (!is_null($exception)) {
					throw $exception;
				}
				/**
				 * END PATCH
				 */
			}
			$this->getOnepage()->getQuote()->collectTotals()->save();
			$this->getResponse()->setBody(df_mage()->coreHelper()->jsonEncode($result));
		}
	}
}