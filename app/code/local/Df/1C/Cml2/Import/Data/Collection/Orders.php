<?php
class Df_1C_Cml2_Import_Data_Collection_Orders extends Df_1C_Cml2_Import_Data_Collection {
	/**
	 * @override
	 * @see Df_Core_Xml_Parser_Collection::itemClass()
	 * @return string
	 */
	protected function itemClass() {return Df_1C_Cml2_Import_Data_Entity_Order::_C;}

	/**
	 * @override
	 * @see Df_Core_Xml_Parser_Collection::itemPath()
	 * @return string|string[]
	 */
	protected function itemPath() {return '/КоммерческаяИнформация/Документ';}

	/**
	 * @used-by Df_1C_Cml2_Action_Orders_Import::getOrders()
	 * @static
	 * @param Df_Core_Sxe $e
	 * @return Df_1C_Cml2_Import_Data_Collection_Orders
	 */
	public static function i(Df_Core_Sxe $e) {return new self(array(self::$P__E => $e));}
}