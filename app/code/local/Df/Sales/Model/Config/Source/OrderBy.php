<?php
/**
 * @singleton
 * Система создаёт объект-одиночку для потомков этого класса.
 * Не забывайте об этом при реализации кеширования результатов вычислений внутри этого класса!
 */
class Df_Sales_Model_Config_Source_OrderBy extends Df_Admin_Config_Source {
	/**
	 * @override
	 * @param bool $isMultiSelect
	 * @return array(array(string => string))
	 */
	protected function toOptionArrayInternal($isMultiSelect = false) {
		return rm_map_to_options(array(
			Df_Sales_Block_Admin_Grid_OrderItem_Collection::ORDER_BY__NAME => 'названиям'
			,Df_Sales_Block_Admin_Grid_OrderItem_Collection::ORDER_BY__SKU => 'артикулам'
			,Df_Sales_Block_Admin_Grid_OrderItem_Collection::ORDER_BY__QTY => 'заказанным количествам'
			,Df_Sales_Block_Admin_Grid_OrderItem_Collection::ORDER_BY__ROW_TOTAL =>
				'стоимостям строк заказа'
		));
	}
	const _C = __CLASS__;
}