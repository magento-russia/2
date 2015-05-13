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
			 * @link http://magento-forum.ru/topic/2394/
			 */
			if (!df_is_admin()) {
				$this->getEvent()->getOrderStatusHistory()
					->setData(
						Df_Sales_Const::ORDER_STATUS_HISTORY_PARAM__IS_VISIBLE_ON_FRONT
						,$this->getEvent()->getOrder()->getData(
							Df_Sales_Model_Order::RM_PARAM__COMMENT_IS_VISIBLE_ON_FRONT
						)
					)
				;
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
						df_cfg()->sales()->orderComments()->adminOrderCreate_commentIsVisibleOnFront()
					&&
						df_enabled(Df_Core_Feature::SALES)
				) {
					if (
							('new' === $this->getEvent()->getOrder()->getState())
						&&
							('pending' === $this->getEvent()->getOrder()->getStatus())
					) {
						$this->getEvent()->getOrderStatusHistory()
							->setData(
								Df_Sales_Const::ORDER_STATUS_HISTORY_PARAM__IS_VISIBLE_ON_FRONT
								,true
							)
						;
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
	protected function getEventClass() {
		return Df_Sales_Model_Event_OrderStatusHistory_SaveBefore::_CLASS;
	}

	const _CLASS = __CLASS__;
}