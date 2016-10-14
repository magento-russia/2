<?php
/** @method Df_Core_Model_Event_Controller_Action_Layout_GenerateBlocksAfter getEvent() */
class Df_Tweaks_Model_Handler_AdjustCartPage extends Df_Core_Model_Handler {
	/**
	 * Метод-обработчик события
	 * @override
	 * @return void
	 */
	public function handle() {
		/** @var Df_Tweaks_Model_Settings_Cart $s */
		$s = Df_Tweaks_Model_Settings_Cart::s();
		if ($s->removeShippingAndTaxEstimation()) {
			df_block_remove('checkout.cart.shipping');
		}
		if ($s->removeDiscountCodesBlock()) {
			df_block_remove('checkout.cart.coupon');
		}
		if ($s->removeCrosssellBlock()) {
			df_block_remove('checkout.cart.crosssell');
		}
	}

	/**
	 * Класс события (для валидации события)
	 * @override
	 * @return string
	 */
	protected function getEventClass() {
		return Df_Core_Model_Event_Controller_Action_Layout_GenerateBlocksAfter::_C;
	}

	/** @used-by Df_Tweaks_Observer::controller_action_layout_generate_blocks_after() */
	const _C = __CLASS__;
}