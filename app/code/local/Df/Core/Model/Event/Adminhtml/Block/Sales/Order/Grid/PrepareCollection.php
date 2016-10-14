<?php
/**
 * Cообщение:		«rm_adminhtml_block_sales_order_grid__prepare_collection»
 * Источник:		Df_Adminhtml_Block_Sales_Order_Grid::setCollection()
 * [code]
		Mage
			::dispatchEvent(
				'rm_adminhtml_block_sales_order_grid__prepare_collection'
				,array(
					'collection' => $collection
				)
			)
		;	
 * [/code]
 */
class Df_Core_Model_Event_Adminhtml_Block_Sales_Order_Grid_PrepareCollection
	extends Df_Core_Model_Event {
	/** @return Mage_Sales_Model_Resource_Order_Grid_Collection|Mage_Sales_Model_Mysql4_Order_Collection */
	public function getCollection() {return $this->getEventParam(self::$E__COLLECTION);}

	/**
	 * @override
	 * @return string
	 */
	protected function getExpectedEventPrefix() {return self::$EVENT;}

	/**
	 * @used-by Df_Sales_Observer::rm_adminhtml_block_sales_order_grid__prepare_collection()
	 * @used-by Df_Sales_Model_Handler_AdminOrderGrid_AddProductDataToCollection::getEventClass()
	 */
	const _C = __CLASS__;
	/** @var string  */
	private static $E__COLLECTION = 'collection';
	/** @var string */
	private static $EVENT = 'rm_adminhtml_block_sales_order_grid__prepare_collection';

	/**
	 * @param Varien_Data_Collection|Mage_Sales_Model_Resource_Order_Grid_Collection|Mage_Sales_Model_Mysql4_Order_Collection $collection
	 * @return void
	 */
	public static function dispatch(Varien_Data_Collection $collection) {
		df_h()->sales()->assert()->orderGridCollection($collection);
		Mage::dispatchEvent(self::$EVENT, array(self::$E__COLLECTION => $collection));
	}
}