<?php
/** @method Df_Checkout_Model_Event_SaveOrder_Abstract getEvent() */
class Df_Checkout_Model_Handler_SaveOrderComment extends Df_Core_Model_Handler {
	/**
	 * Метод-обработчик события
	 * @override
	 * @return void
	 */
	public function handle() {
		if ($this->getOrderComment() && df_cfgr()->checkout()->orderComments()->isEnabled()) {
			$this->getEvent()->getOrder()->addData(array(
				/**
				 * Устанавка «customer note»
				 * приводит в числе прочего к вызову @see Mage_Sales_Model_Order::addStatusHistoryComment()
				 */
				Df_Sales_Model_Order::P__CUSTOMER_NOTE => $this->getOrderComment()
				,Df_Sales_Model_Order::P__CUSTOMER_NOTE_NOTIFY =>
					df_cfgr()->checkout()->orderComments()->needShowInOrderEmail()
			));
			$this->getEvent()->getOrder()->setCommentToBeVisibleOnFront(
				df_cfgr()->checkout()->orderComments()->needShowInCustomerAccount()
			);
		}
	}

	/**
	 * Класс события (для валидации события)
	 * @override
	 * @return string
	 */
	protected function getEventClass() {return Df_Checkout_Model_Event_SaveOrder_Abstract::class;}

	/** @return string */
	private function getOrderComment() {return df_trim(df_request('df_order_comment', ''));}

	/**
	 * @used-by Df_Checkout_Observer::checkout_type_multishipping_create_orders_single()
	 * @used-by Df_Checkout_Observer::checkout_type_onepage_save_order()
	 */

}