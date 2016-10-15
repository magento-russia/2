<?php
class Df_1C_Cml2_Import_Data_Collection_Products extends Df_1C_Cml2_Import_Data_Collection {
	/**
	 * @override
	 * @see \Df\Xml\Parser\Collection::itemClass()
	 * @return string
	 */
	protected function itemClass() {return Df_1C_Cml2_Import_Data_Entity_Product::_C;}

	/**
	 * @override
	 * @see \Df\Xml\Parser\Collection::itemPath()
	 * @return string|string[]
	 */
	protected function itemPath() {return '/КоммерческаяИнформация/Каталог/Товары/Товар';}

	/**
	 * @used-by Df_1C_Cml2_State_Import_Collections::getProducts()
	 * @static
	 * @param \Df\Xml\X $e
	 * @return Df_1C_Cml2_Import_Data_Collection_Products
	 */
	public static function i(\Df\Xml\X $e) {return new self(array(self::$P__E => $e));}
}