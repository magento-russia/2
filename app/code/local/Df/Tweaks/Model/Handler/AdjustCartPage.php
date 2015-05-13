<?php
/**
 * @method Df_Core_Model_Event_Controller_Action_Layout_GenerateBlocksAfter getEvent()
 */
class Df_Tweaks_Model_Handler_AdjustCartPage extends Df_Core_Model_Handler {
	/**
	 * Метод-обработчик события
	 * @override
	 * @return void
	 */
	public function handle() {
		if (
			df_cfg()->tweaks()->checkout()->cart()
				->removeShippingAndTaxEstimation()
		) {
			df()->layout()->removeBlock('checkout.cart.shipping');
		}
		if (
			df_cfg()->tweaks()->checkout()->cart()
				->removeDiscountCodesBlock()
		) {
			df()->layout()->removeBlock('checkout.cart.coupon');
		}
		if (
			df_cfg()->tweaks()->checkout()->cart()
				->removeCrosssellBlock()
		) {
			df()->layout()->removeBlock('checkout.cart.crosssell');
		}
	}

	/**
	 * Класс события (для валидации события)
	 * @override
	 * @return string
	 */
	protected function getEventClass() {
		return Df_Core_Model_Event_Controller_Action_Layout_GenerateBlocksAfter::_CLASS;
	}

	const _CLASS = __CLASS__;
}