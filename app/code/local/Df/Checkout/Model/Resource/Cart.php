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
	public function addExcludeProductFilter($collection, $quoteId) {
		$adapter = $this->_getReadAdapter();
		$exclusionSelect = $adapter->select()
			->from(rm_table('sales/quote_item'), 'product_id')
			->where('? = quote_id', $quoteId);
		$condition =
			$adapter->prepareSqlCondition(
				'e.entity_id'
				,array(
					 'nin'
					=>
					 // НАЧАЛО ЗАПЛАТКИ
					  $exclusionSelect->__toString()
					 // КОНЕЦ ЗАПЛАТКИ
				)
			)
		;
		$collection->getSelect()->where($condition);
		return $this;
	}

	/**
	 * 2015-02-09
	 * Возвращаем объект-одиночку именно таким способом,
	 * потому что наш класс перекрывает посредством <rewrite> системный класс,
	 * и мы хотим, чтобы вызов @see Mage::getResourceSingleton() ядром Magento
	 * возвращал тот же объект, что и наш метод @see s(),
	 * сохраняя тем самым объект одиночкой (это важно, например, для производительности:
	 * сохраняя объект одиночкой — мы сохраняем его кэш между всеми пользователями объекта).
	 * @return Df_Checkout_Model_Resource_Cart
	 */
	public static function s() {return Mage::getResourceSingleton('checkout/cart');}
}