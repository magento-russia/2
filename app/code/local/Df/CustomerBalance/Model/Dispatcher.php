<?php
class Df_CustomerBalance_Model_Dispatcher {
	/**
	 * @param Varien_Event_Observer $observer
	 * @return void
	 */
	public function core_block_abstract_to_html_after(
		Varien_Event_Observer $observer
	) {
		try {
			/** @var Mage_Core_Block_Abstract $block */
			$block = $observer->getData('block');
			if ('checkout.payment.methods' === $block->getNameInLayout()) {
				/** @var Varien_Object $transport */
				$transport = $observer->getData('transport');
				df_assert($transport instanceof Varien_Object);
				/** @var string $html */
				$html = $transport->getData('html');
				$html =
					df_concat(
						$this->getBlockCustomerBalance()->toHtml()
						,$html
						,$this->getBlockCustomerBalanceScripts()->toHtml()
					)					
				;
				$transport->setData('html', $html);
			}
		}

		catch(Exception $e) {
			df_handle_entry_point_exception($e);
		}

	}

	/** @return Df_CustomerBalance_Block_Checkout_Onepage_Payment_Additional */
	private function getBlockCustomerBalance() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				Df_CustomerBalance_Block_Checkout_Onepage_Payment_Additional::i('customerbalance')
			;
			$this->{__METHOD__}->setTemplate(
				'df/customerbalance/checkout/onepage/payment/additional.phtml'
			);
		}
		return $this->{__METHOD__};
	}

	/** @return Df_CustomerBalance_Block_Checkout_Onepage_Payment_Additional */
	private function getBlockCustomerBalanceScripts() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				Df_CustomerBalance_Block_Checkout_Onepage_Payment_Additional::i('customerbalance_scripts')
			;
			$this->{__METHOD__}->setTemplate('df/customerbalance/checkout/onepage/payment/scripts.phtml');
		}
		return $this->{__METHOD__};
	}
}