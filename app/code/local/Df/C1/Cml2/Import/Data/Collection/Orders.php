<?php
namespace Df\C1\Cml2\Import\Data\Collection;
class Df_C1_Cml2_Import_Data_Collection_Orders extends Df_C1_Cml2_Import_Data_Collection {
	/**
	 * @override
	 * @see \Df\Xml\Parser\Collection::itemClass()
	 * @return string
	 */
	protected function itemClass() {return Df_C1_Cml2_Import_Data_Entity_Order::class;}

	/**
	 * @override
	 * @see \Df\Xml\Parser\Collection::itemPath()
	 * @return string|string[]
	 */
	protected function itemPath() {return '/КоммерческаяИнформация/Документ';}

	/**
	 * @used-by Df_C1_Cml2_Action_Orders_Import::getOrders()
	 * @static
	 * @param \Df\Xml\X $e
	 * @return Df_C1_Cml2_Import_Data_Collection_Orders
	 */
	public static function i(\Df\Xml\X $e) {return new self(array(self::$P__E => $e));}
}