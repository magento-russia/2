<?php
/**
 * Для данного множества подарков возвращает соответствующее ему множество товаров
 */
class Df_PromoGift_Model_Filter_Gift_Collection_MapToProducts
	extends Df_Core_Model
	implements Zend_Filter_Interface {
	/**
	 *
	 * @param Df_Varien_Data_Collection $value
	 * @throws Zend_Filter_Exception If filtering $value is impossible
	 * @return Df_Catalog_Model_Resource_Product_Collection
	 */
	public function filter($value) {
		df_assert($value instanceof Df_Varien_Data_Collection);
		// А вот здесь мы можем создать коллекцию товаров
		/** @var Df_Catalog_Model_Resource_Product_Collection $result */
		$productIds = $value->getColumnValues(Df_PromoGift_Const::DB__PROMO_GIFT__PRODUCT_ID);
		$result = Df_Catalog_Model_Product::c();
		$result->addAttributeToSelect('*');
		$result->addIdFilter(array_values($productIds));
		$result->addIdFilter(array_values($productIds));
		/**
		 * Иначе адреса будут вида
		 * http://example.com/catalog/product/view/id/119/s/coalesce-shirt/category/34/
		 */
		$result->addUrlRewrite();
		$result->load();
		return $result;
	}

	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_PromoGift_Model_Filter_Gift_Collection_MapToProducts
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}