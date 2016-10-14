<?php
class Df_Sales_Block_Admin_Grid_OrderItem extends Df_Core_Block_Admin {
	/** @return int */
	public function getOrderItemId() {return $this->cfg(self::P__ORDER_ITEM_ID);}

	/** @return int */
	public function getProductId() {return $this->cfg(self::P__PRODUCT_ID);}

	/** @return string */
	public function getProductName() {return $this->cfg(self::P__PRODUCT_NAME);}

	/** @return string */
	public function getProductNameToDisplay() {
		return
			!$this->settings()->needChopName()
			? $this->getProductName()
			: df_t()->chop($this->getProductName(), $this->settings()->getProductNameMaxLength())
		;
	}

	/**
	 * Как ни странно, система может передать вес в виде строки вида «1.0000».
	 * http://magento-forum.ru/topic/4532/
	 * По этой причине используем преобразование типов.
	 * @return int
	 */
	public function getProductQty() {return (int)floatval($this->cfg(self::P__PRODUCT_QTY));}

	/** @return string */
	public function getProductSku() {return $this->cfg(self::P__PRODUCT_SKU);}

	/** @return float */
	public function getRowTotal() {return $this->cfg(self::P__ROW_TOTAL);}

	/**
	 * @override
	 * @see Df_Core_Block_Template::defaultTemplate()
	 * @used-by Df_Core_Block_Template::getTemplate()
	 * @return string
	 */
	protected function defaultTemplate() {return 'df/sales/grid/orderItem.phtml';}

	/** @return Df_Sales_Model_Settings_OrderGrid_ProductColumn */
	private function settings() {return Df_Sales_Model_Settings_OrderGrid_ProductColumn::s();}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__PRODUCT_NAME, RM_V_STRING_NE)
			->_prop(self::P__PRODUCT_SKU, RM_V_STRING_NE)
			/**
			 * Как ни странно, система может передать вес в виде строки вида «1.0000».
			 * http://magento-forum.ru/topic/4532/
			 * По этой причине для свойства self::P__PRODUCT_QTY
			 * нельзя использовать валидатор RM_V_NAT0.
			 */
			->_prop(self::P__PRODUCT_QTY, RM_V_STRING_NE)
			->_prop(self::P__ORDER_ITEM_ID, RM_V_NAT)
			->_prop(self::P__PRODUCT_ID, RM_V_NAT)
			->_prop(self::P__ROW_TOTAL, RM_V_FLOAT)
		;
	}

	/** @used-by Df_Sales_Block_Admin_Grid_OrderItem_Collection::itemClass() */
	const _C = __CLASS__;
	const COLLECTION_ITEM_PARAM__PARENT_ID = 'parent_id';
	const P__PRODUCT_ID = 'product_id';
	const P__ORDER_ITEM_ID = 'order_item_id';
	const P__PRODUCT_NAME = 'product_name';
	const P__PRODUCT_QTY = 'product_qty';
	const P__PRODUCT_SKU = 'product_sku';
	const P__ROW_TOTAL = 'row_total';
	/**
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Sales_Block_Admin_Grid_OrderItem
	 */
	public static function i($parameters) {return new self($parameters);}

	/** @return string[] */
	public static function getKeysSource() {
		static $r; return $r ? $r : $r = array_keys(self::getMapFromSourceKeysToTargetKeys());
	}

	/** @return string[] */
	public static function getKeysTarget() {
		static $r; return $r ? $r : $r = array_values(self::getMapFromSourceKeysToTargetKeys());
	}

	/** @return array(string => string) */
	private static function getMapFromSourceKeysToTargetKeys() {
		static $r; return $r ? $r : $r = array(
			Df_Sales_Model_Handler_AdminOrderGrid_AddProductDataToCollection
				::COLLECTION_ITEM_PARAM__DF_NAMES
				=>
			self::P__PRODUCT_NAME
			,Df_Sales_Model_Handler_AdminOrderGrid_AddProductDataToCollection
						::COLLECTION_ITEM_PARAM__DF_SKUS
				=>
				self::P__PRODUCT_SKU
			,Df_Sales_Model_Handler_AdminOrderGrid_AddProductDataToCollection
						::COLLECTION_ITEM_PARAM__DF_QTYS
				=>
				self::P__PRODUCT_QTY
			,Df_Sales_Model_Handler_AdminOrderGrid_AddProductDataToCollection
						::COLLECTION_ITEM_PARAM__DF_TOTALS
				=>
				self::P__ROW_TOTAL
			,Df_Sales_Model_Handler_AdminOrderGrid_AddProductDataToCollection
						::COLLECTION_ITEM_PARAM__DF_PRODUCT_IDS
				=>
				self::P__PRODUCT_ID
			,Df_Sales_Model_Handler_AdminOrderGrid_AddProductDataToCollection
						::COLLECTION_ITEM_PARAM__DF_ORDER_ITEM_IDS
				=>
				self::P__ORDER_ITEM_ID
			,Df_Sales_Model_Handler_AdminOrderGrid_AddProductDataToCollection
						::COLLECTION_ITEM_PARAM__DF_PARENTS
				=>
					self::COLLECTION_ITEM_PARAM__PARENT_ID
		);
	}
}