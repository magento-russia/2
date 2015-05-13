<?php
/**
 * Обновляет в заказе (PARAM__ENTITY_ORDER) строку (PARAM__ENTITY)
 * на основании пришедших из 1С:Управление торговлей данных
 */
class Df_1C_Model_Cml2_Import_Processor_Order_Item_Update
	extends Df_1C_Model_Cml2_Import_Processor_Order_Item {
	/**
	 * @override
	 * @return void
	 */
	public function process() {}

	/**
	 * Используется из @see Df_1C_Model_Cml2_Import_Processor_Order::orderItemsUpdate()
	 */
	const _CLASS = __CLASS__;
}