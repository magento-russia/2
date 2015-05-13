<?php
class Df_Checkout_Model_Resource_Cart extends Mage_Checkout_Model_Mysql4_Cart {
	/**
	 * Цель перекрытия —
	 * устранение сбоя, который, видимо, иногда происходил в методе
	 * @see Mage_Checkout_Model_Mysql4_Cart::addExcludeProductFilter()
	 * @override
	 * @param Mage_Catalog_Model_Resource_Product_Collection $collection
	 * @param int $quoteId
	 * @return Mage_Checkout_Model_Resource_Cart
	 */
	public function addExcludeProductFilter($collection, $quoteId)
	{
		$adapter = $this->_getReadAdapter();
		$exclusionSelect = $adapter->select()
			->from(rm_table('sales/quote_item'), array('product_id'))
			->where('quote_id = ?', $quoteId);
		$condition =
			$adapter->prepareSqlCondition(
				'e.entity_id'
				,array(
					 'nin'
					=>
					 /**
					  * BEGIN PATCH
					  */
					  $exclusionSelect->__toString()
					 /**
					  * END PATCH
					  */
				)
			)
		;
		$collection->getSelect()->where($condition);
		return $this;
	}

}