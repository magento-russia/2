<?php
class Df_Sales_Setup_2_23_5 extends Df_Core_Setup {
	/**
	 * Переводим англоязычные названия состояний заказа на русский язык.
	 *
	 * Обратите внимание, что в Magento CE 1.4 отсутствует класс
	 * @see Mage_Sales_Model_Mysql4_Order_Status,
	 * от которого унаследован наш класс @see Df_Sales_Model_Resource_Order_Status
	 * Это связано с тем, что в Magento CE 1.4 в БД отсутствует справочник состояний заказа.
	 *
	 * 2016-10-16
	 * Magento CE 1.4 отныне не поддерживаем.
	 *
	 * @override
	 * @see Df_Core_Setup::_process()
	 * @used-by Df_Core_Setup::process()
	 * @return void
	 */
	protected function _process() {
		foreach (Df_Sales_Model_Order_Status::c() as $status) {
			/** @var Df_Sales_Model_Order_Status $status */
			$status->setLabel($this->translate($status->getLabel()))->save();
		}
	}

	/**
	 * @param string $name
	 * @return string
	 */
	private function translate($name) {
		return
			dfa(
				array(
					'Processing' => 'заказ выполняется'
					,'Pending Payment' => 'ждём оплату'
					,'Suspected Fraud' => 'возможное мошенничество'
					,'Payment Review' => 'модерация платежа'
					,'Pending' => 'модерация заказа'
					,'On Hold' => 'заказ заморожен'
					,'Complete' => 'заказ выполнен'
					,'Closed' => 'заказ закрыт'
					,'Canceled' => 'заказ отменён'
					,'Pending PayPal' => 'ожидаем оплату через PayPal'
					,'PayPal Reversed' => 'выполнен возврат оплаты через PayPal'
					,'PayPal Canceled Reversal' => 'неудачный возврат оплаты через PayPal'
				)
				,$name
				,$name
		);
	}
}