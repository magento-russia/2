<?php
class Df_Sales_Block_Admin_Widget_Grid_Column_RendererDf_Products_Product extends Df_Core_Block_Admin {
	/** @return int */
	public function getOrderItemId() {return $this->cfg(self::P__ORDER_ITEM_ID);}
	/** @return int */
	public function getProductId() {return $this->cfg(self::P__PRODUCT_ID);}
	/** @return string */
	public function getProductName() {return $this->cfg(self::P__PRODUCT_NAME);}
	/** @return string */
	public function getProductNameToDisplay() {
		return
			!df_cfg()->sales()->orderGrid()->productColumn()->needChopName()
			? $this->getProductName()
			: df_text()->chop(
				$this->getProductName()
				,df_cfg()->sales()->orderGrid()->productColumn()->getProductNameMaxLength()
			)
		;
	}
	/** @return int */
	public function getProductQty() {
		/**
		 * Как ни странно, система может передать вес в виде строки вида «1.0000».
		 * @link http://magento-forum.ru/topic/4532/
		 * По этой причине используем преобразование типов.
		 */
		return intval(floatval($this->cfg(self::P__PRODUCT_QTY)));
	}

	/** @return string */
	public function getProductSku() {return $this->cfg(self::P__PRODUCT_SKU);}
	/** @return float */
	public function getRowTotal() {return $this->cfg(self::P__ROW_TOTAL);}

	/**
	 * @override
	 * @return string|null
	 */
	protected function getDefaultTemplate() {return self::DEFAULT_TEMPLATE;}
	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__PRODUCT_NAME, self::V_STRING_NE)
			->_prop(self::P__PRODUCT_SKU, self::V_STRING_NE)
			/**
			 * Как ни странно, система может передать вес в виде строки вида «1.0000».
			 * @link http://magento-forum.ru/topic/4532/
			 * По этой причине для свойства self::P__PRODUCT_QTY
			 * нельзя использовать валидатор self::V_NAT0.
			 */
			->_prop(self::P__PRODUCT_QTY, self::V_STRING_NE)
			->_prop(self::P__ORDER_ITEM_ID, self::V_NAT)
			->_prop(self::P__PRODUCT_ID, self::V_NAT)
			->_prop(self::P__ROW_TOTAL, self::V_FLOAT)
		;
	}
	const _CLASS = __CLASS__;
	const DEFAULT_TEMPLATE = 'df/sales/widget/grid/column/renderer/products/product.phtml';
	const P__PRODUCT_ID = 'product_id';
	const P__ORDER_ITEM_ID = 'order_item_id';
	const P__PRODUCT_NAME = 'product_name';
	const P__PRODUCT_QTY = 'product_qty';
	const P__PRODUCT_SKU = 'product_sku';
	const P__ROW_TOTAL = 'row_total';
	/**
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Sales_Block_Admin_Widget_Grid_Column_RendererDf_Products_Product
	 */
	public static function i($parameters) {return df_block(new self($parameters));}
}