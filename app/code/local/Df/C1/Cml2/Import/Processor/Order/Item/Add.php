<?php
// добавляет к заказу новую позицию на основании пришедших из 1С:Управление торговлей данных
class Df_C1_Cml2_Import_Processor_Order_Item_Add extends Df_C1_Cml2_Import_Processor_Order_Item {
	/**
	 * @override
	 * @return void
	 */
	public function process() {}

	/** @used-by Df_C1_Cml2_Import_Processor_Order::orderItemsAdd() */

}