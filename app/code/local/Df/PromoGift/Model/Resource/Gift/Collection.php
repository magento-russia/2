<?php
class Df_PromoGift_Model_Resource_Gift_Collection extends Df_Core_Model_Resource_Collection {
	/**
	 * @param int $ruleId
	 * @return Df_PromoGift_Model_Resource_Gift_Collection
	 */
	public function addRuleFilter($ruleId) {
		$this->addFieldToFilter(Df_PromoGift_Model_Gift::P__RULE_ID, array('eq' => $ruleId));
		return $this;
	}

	/**
	 * Отбраковываем неотносящиеся к магазину правила
	 * @param int $websiteId
	 * @return Df_PromoGift_Model_Resource_Gift_Collection
	 */
	public function addWebsiteFilter($websiteId) {
		$this->addFieldToFilter(Df_PromoGift_Model_Gift::P__WEBSITE_ID, array('eq' => $websiteId));
		return $this;
	}

	/**
	 * Для данного множества подарков возвращает соответствующее ему множество товаров.
	 * Конечно, объекты класса @see Df_PromoGift_Model_Gift умеют сами загужать
	 * относящиеся к ним модели (правило, товар, сайт),
	 * но если они будут делать это по-отдельности — они создадут много запросов к БД.
	 * Эффективней явно дать им нужные модели.
	 * @return Df_Catalog_Model_Resource_Product_Collection
	 */
	public function getProducts() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Catalog_Model_Resource_Product_Collection $result */
			$result = Df_Catalog_Model_Product::c();
			$result->addAttributeToSelect('*');
			$result->addIdFilter(
				array_values($this->getColumnValues(Df_PromoGift_Model_Gift::P__PRODUCT_ID))
			);
			/**
			 * Иначе адреса будут вида
			 * http://example.com/catalog/product/view/id/119/s/coalesce-shirt/category/34/
			 */
			$result->addUrlRewrite();
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return Df_PromoGift_Model_Resource_Gift
	 */
	public function getResource() {return Df_PromoGift_Model_Resource_Gift::s();}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {$this->_itemObjectClass = Df_PromoGift_Model_Gift::class;}

}