<?php
require_once 'Mage/Checkout/controllers/OnepageController.php';
class Df_Checkout_OnepageController extends Mage_Checkout_OnepageController {
	/**
	 * @override
	 * @see Mage_Checkout_OnepageController::getOnepage()
	 * @return Df_Checkout_Model_Type_Onepage
	 */
	public function getOnepage() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Checkout_Model_Type_Onepage $result */
			$result = parent::getOnepage();
			$result->setController($this);
			$this->{__METHOD__} = $result;
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
				Mage::dispatchEvent('checkout_controller_onepage_save_shipping_method', array(
					'request'=>$this->getRequest()
					,'quote'=>$this->getOnepage()->getQuote()
				));
				$this->getOnepage()->getQuote()->collectTotals();
				$this->getResponse()->setBody(df_mage()->coreHelper()->jsonEncode($result));
				$result['goto_section'] = 'payment';
				$result['update_section'] = array(
					'name' => 'payment-method','html' => $this->_getPaymentMethodsHtml()
				);
				/**
				 * BEGIN PATCH
				 */
				df_cache()->banUse('layout');
				/** @var Exception $exception|null */
				$exception = null;
				try {
					$this->loadLayout('checkout_onepage_review');
					$result['df_update_sections'] = [[
						'name' => 'review',
						'html' => $this->getLayout()->getBlock('root')->toHtml()
					]];
				}
				catch (Exception $e) {
					$exception = $e;
				}
				if (!is_null($exception)) {
					df_error($exception);
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