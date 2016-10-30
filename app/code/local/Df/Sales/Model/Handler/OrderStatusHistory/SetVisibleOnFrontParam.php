<?php
/**
 * @method Df_Sales_Model_Event_OrderStatusHistory_SaveBefore getEvent()
 */
class Df_Sales_Model_Handler_OrderStatusHistory_SetVisibleOnFrontParam extends Df_Core_Model_Handler {
	/**
	 * Метод-обработчик события
	 * @override
	 * @return void
	 */
	public function handle() {
		if (!is_null($this->getEvent()->getOrder())) {
			/**
			 * Проверка обязательна,
			 * иначе некорректно работает добавление комментариев администратором
			 * http://magento-forum.ru/topic/2394/
			 */
			if (!df_is_admin()) {
				$this->getEvent()->getOrderStatusHistory()->setIsVisibleOnFront(
					$this->getEvent()->getOrder()->needCommentToBeVisibleOnFront()
				);
			}
			else {
				/**
				 * Обратите внимание,
				 * что если заказ только что создан из административной части,
				 * то комментарий к нему не будет виден клиентом —
				 * это стандартное поведение Magento.
				 *
				 * Можно опционально его изменить...
				 */
				if (
					df_cfgr()->sales()->orderComments()
						->adminOrderCreate_commentIsVisibleOnFront()
				) {
					if (
						'new' === $this->getEvent()->getOrder()->getState()
						&& 'pending' === $this->getEvent()->getOrder()->getStatus()
					) {
						$this->getEvent()->getOrderStatusHistory()->setIsVisibleOnFront(true);
					}
				}
			}
		}
	}

	/**
	 * Класс события (для валидации события)
	 * @override
	 * @return string
	 */
	protected function getEventClass() {return Df_Sales_Model_Event_OrderStatusHistory_SaveBefore::class;}

	/** @used-by Df_Sales_Observer::sales_order_status_history_save_before() */

}