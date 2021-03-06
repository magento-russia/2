<?php
class Df_Sales_Model_Settings_OrderGrid_ProductColumn extends Df_Core_Model_Settings {
	/** @return boolean */
	public function getEnabled() {return $this->getYesNo('enabled');}
	/** @return int */
	public function getMaxProductsToShow() {return $this->getNatural('max_products_to_show');}
	/** @return int */
	public function getNameWidth() {return $this->getNatural('name_width');}
	/** @return string */
	public function getOrderBy() {return $this->getString('order_by');}
	/** @return string */
	public function getOrderDirection() {return $this->getString('order_direction');}
	/** @return int */
	public function getOrdering() {return $this->getInteger('ordering');}
	/** @return int */
	public function getProductNameMaxLength() {return $this->getNatural('product_name_max_length');}
	/** @return int */
	public function getQtyWidth() {return $this->getNatural('qty_width');}
	/** @return int */
	public function getSkuWidth() {return $this->getNatural('sku_width');}
	/** @return boolean */
	public function needChopName() {return $this->getYesNo('chop_name');}
	/** @return boolean */
	public function needShowName() {return $this->getYesNo('show_name');}
	/** @return boolean */
	public function needShowQty() {return $this->getYesNo('show_qty');}
	/** @return boolean */
	public function needShowSku() {return $this->getYesNo('show_sku');}
	/** @return boolean */
	public function showAllProducts() {return $this->getYesNo('show_all_products');}
	/**
	 * @override
	 * @return string
	 */
	protected function getKeyPrefix() {return 'df_sales/order_grid__product_column/';}
	/** @return Df_Sales_Model_Settings_OrderGrid_ProductColumn */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}