<?php
/**
 * @singleton
 * Система создаёт объект-одиночку для потомков этого класса.
 * Не забывайте об этом при реализации кеширования результатов вычислений внутри этого класса!
 */
class Df_Sales_Model_System_Config_Source_OrderGridColumnProductNames_OrderBy
	extends Df_Admin_Model_Config_Source {
	/**
	 * @override
	 * @param bool $isMultiSelect
	 * @return array(array(string => string))
	 */
	protected function toOptionArrayInternal($isMultiSelect = false) {
		return
			array(
				array(
					self::OPTION_KEY__LABEL => 'названиям'
					,self::OPTION_KEY__VALUE =>
						Df_Sales_Model_Presentation_OrderGrid_CellData_Products_Collection
							::ORDER_BY__NAME
				)
				,array(
					self::OPTION_KEY__LABEL => 'артикулам'
					,self::OPTION_KEY__VALUE =>
						Df_Sales_Model_Presentation_OrderGrid_CellData_Products_Collection
							::ORDER_BY__SKU
				)
				,array(
					self::OPTION_KEY__LABEL => 'заказанным количествам'
					,self::OPTION_KEY__VALUE =>
						Df_Sales_Model_Presentation_OrderGrid_CellData_Products_Collection
							::ORDER_BY__QTY
				)
				,array(
					self::OPTION_KEY__LABEL => 'стоимостям строк заказа'
					,self::OPTION_KEY__VALUE =>
						Df_Sales_Model_Presentation_OrderGrid_CellData_Products_Collection
							::ORDER_BY__ROW_TOTAL
				)
			)
		;
	}
	const _CLASS = __CLASS__;
}