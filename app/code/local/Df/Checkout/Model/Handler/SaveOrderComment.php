<?php
/**
 * @method Df_Checkout_Model_Event_SaveOrder_Abstract getEvent()
 */
class Df_Checkout_Model_Handler_SaveOrderComment extends Df_Core_Model_Handler {
	/**
	 * Метод-обработчик события
	 * @override
	 * @return void
	 */
	public function handle() {
		if (
				$this->getOrderComment()
			&&
				df_cfg()->checkout()->orderComments()->isEnabled()
			&&
				df_enabled(Df_Core_Feature::CHECKOUT)
		) {
			$this->getEvent()->getOrder()->addData(
				array(
					/**
					 * Устанавка customer note
					 * приводит в числе прочего к вызову addStatusHistoryComment
					 */
					Df_Sales_Model_Order::P__CUSTOMER_NOTE => $this->getOrderComment()
					,Df_Sales_Model_Order::P__CUSTOMER_NOTE_NOTIFY =>
						df_cfg()->checkout()->orderComments()->needShowInOrderEmail()
					,Df_Sales_Model_Order::RM_PARAM__COMMENT_IS_VISIBLE_ON_FRONT =>
						df_cfg()->checkout()->orderComments()->needShowInCustomerAccount()
				)
			);
		}
	}

	/**
	 * Класс события (для валидации события)
	 * @override
	 * @return string
	 */
	protected function getEventClass() {
		return Df_Checkout_Model_Event_SaveOrder_Abstract::_CLASS;
	}

	/** @return string */
	private function getOrderComment() {
		return df_trim(df_request(self::REQUEST_PARAM__ORDER_COMMENT, ''));
	}

	const _CLASS = __CLASS__;
	const REQUEST_PARAM__ORDER_COMMENT = 'df_order_comment';
}