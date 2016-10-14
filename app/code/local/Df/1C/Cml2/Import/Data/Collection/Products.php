<?php
class Df_1C_Cml2_Import_Data_Collection_Products extends Df_1C_Cml2_Import_Data_Collection {
	/**
	 * @override
	 * @see Df_Core_Xml_Parser_Collection::itemClass()
	 * @return string
	 */
	protected function itemClass() {return Df_1C_Cml2_Import_Data_Entity_Product::_C;}

	/**
	 * @override
	 * @see Df_Core_Xml_Parser_Collection::itemPath()
	 * @return string|string[]
	 */
	protected function itemPath() {return '/КоммерческаяИнформация/Каталог/Товары/Товар';}

	/**
	 * @used-by Df_1C_Cml2_State_Import_Collections::getProducts()
	 * @static
	 * @param Df_Core_Sxe $e
	 * @return Df_1C_Cml2_Import_Data_Collection_Products
	 */
	public static function i(Df_Core_Sxe $e) {return new self(array(self::$P__E => $e));}
}