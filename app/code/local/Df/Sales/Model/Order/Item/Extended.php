<?php
class Df_Sales_Model_Order_Item_Extended extends Df_Core_Model {
	/** @return float */
	public function getAmountRefunded() {return $this->_getF('amount_refunded');}
	/** @return float */
	public function getBaseAmountRefunded() {return $this->_getF('base_amount_refunded');}
	/** @return float */
	public function getBaseDiscountInvoiced() {return $this->_getF('base_discount_invoiced');}
	/** @return float */
	public function getBaseDiscountRefunded() {return $this->_getF('base_discount_refunded');}
	/** @return float */
	public function getBaseHiddenTaxAmount() {return $this->_getF('base_hidden_tax_amount');}
	/** @return float */
	public function getBaseHiddenTaxInvoiced() {return $this->_getF('base_hidden_tax_invoiced');}
	/** @return float */
	public function getBaseHiddenTaxRefunded() {return $this->_getF('base_hidden_tax_refunded');}
	/** @return float */
	public function getBaseOriginalPrice() {return $this->_getF('base_original_price');}
	/** @return float */
	public function getBasePrice() {return $this->_getF('base_price');}
	/** @return float */
	public function getBasePriceInclTax() {return $this->_getF('base_price_incl_tax');}
	/** @return float */
	public function getBaseRowInvoiced() {return $this->_getF('base_row_invoiced');}
	/** @return float */
	public function getBaseRowTotal() {return $this->_getF('base_row_total');}
	/** @return float */
	public function getBaseRowTotalInclTax() {return $this->_getF('base_row_total_incl_tax');}
	/** @return float */
	public function getBaseTaxAmount() {return $this->_getF('base_tax_amount');}
	/** @return float */
	public function getBaseTaxBeforeDiscount() {return $this->_getF('base_tax_before_discount');}
	/** @return float */
	public function getBaseTaxInvoiced() {return $this->_getF('base_tax_invoiced');}
	/** @return float */
	public function getBaseTaxRefunded() {return $this->_getF('base_tax_refunded');}
	/** @return float */
	public function getBaseWeeeTaxAppliedAmount() {return $this->_getF('base_weee_tax_applied_amount');}
	/** @return float */
	public function getBaseWeeeTaxAppliedRowAmount() {return $this->_getF('base_weee_tax_applied_row_amnt');}
	/** @return float */
	public function getDiscountAmount() {return $this->_getF('discount_amount');}
	/** @return float */
	public function getDiscountPercent() {return $this->_getF('discount_percent');}
	/** @return float */
	public function getDiscountRefunded() {return $this->_getF('discount_refunded');}
	/** @return float */
	public function getHiddenTaxAmount() {return $this->_getF('hidden_tax_amount');}
	/** @return float */
	public function getHiddenTaxCanceled() {return $this->_getF('hidden_tax_canceled');}
	/** @return float */
	public function getHiddenTaxInvoiced() {return $this->_getF('hidden_tax_invoiced');}
	/** @return float */
	public function getHiddenTaxRefunded() {return $this->_getF('hidden_tax_refunded');}
	/** @return float */
	public function getOriginalPrice() {return $this->_getF('original_price');}
	/** @return float */
	public function getPrice() {return $this->_getF('price');}
	/** @return float */
	public function getQtyCanceled() {return $this->_getF('qty_canceled');}
	/** @return float */
	public function getQtyInvoiced() {return $this->_getF('qty_invoiced');}
	/** @return float */
	public function getQtyOrdered() {return $this->_getF('qty_ordered');}
	/** @return float */
	public function getQtyRefunded() {return $this->_getF('qty_refunded');}
	/** @return float */
	public function getQtyShipped() {return $this->_getF('qty_shipped');}
	/** @return float */
	public function getPriceInclTax() {return $this->_getF('price_incl_tax');}
	/** @return float */
	public function getRowInvoiced() {return $this->_getF('row_invoiced');}
	/** @return float */
	public function getRowTotal() {return $this->_getF('row_total');}
	/** @return float */
	public function getRowTotalInclTax() {return $this->_getF('row_total_incl_tax');}
	/** @return float */
	public function getRowWeight() {return $this->_getF('row_weight');}
	/** @return float */
	public function getTaxAmount() {return $this->_getF('tax_amount');}
	/** @return float */
	public function getTaxBeforeDiscount() {return $this->_getF('tax_before_discount');}
	/** @return float */
	public function getTaxCanceled() {return $this->_getF('tax_canceled');}
	/** @return float */
	public function getTaxInvoiced() {return $this->_getF('tax_invoiced');}
	/** @return float */
	public function getTaxPercent() {return $this->_getF('tax_percent');}
	/** @return float */
	public function getTaxRefunded() {return $this->_getF('tax_refunded');}
	/** @return float */
	public function getWeeeTaxAppliedAmount() {return $this->_getF('weee_tax_applied_amount');}
	/** @return float */
	public function getWeeeTaxAppliedRowAmount() {return $this->_getF('weee_tax_applied_row_amount');}
	/** @return float */
	public function getWeight() {return $this->_getF('weight');}
	/**
	 * @param string $key
	 * @return mixed
	 */
	private function _get($key) {
		// Не знаем тип результата, поэтому валидацию результата не проводим
		return
			$this->getParent()
			? $this->getParent()->getDataUsingMethod($key)
			: $this->getOrderItem()->getDataUsingMethod($key)
		;
	}

	/**
	 * @param string $key
	 * @return float
	 */
	private function _getF($key) {return rm_float($this->_get($key));}

	/** @return Mage_Sales_Model_Order_Item */
	private function getOrderItem() {return $this->cfg(self::P__ORDER_ITEM);}

	/** @return Mage_Sales_Model_Order_Item|null */
	private function getParent() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_n_set($this->getOrderItem()->getParentItem());
		}
		return df_n_get($this->{__METHOD__});
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__ORDER_ITEM, 'Mage_Sales_Model_Order_Item');
	}
	const _C = __CLASS__;
	const P__ORDER_ITEM = 'order_item';
	/**
	 * @static
	 * @param Mage_Sales_Model_Order_Item $orderItem
	 * @return Df_Sales_Model_Order_Item_Extended
	 */
	public static function i(Mage_Sales_Model_Order_Item $orderItem) {
		return new self(array(self::P__ORDER_ITEM => $orderItem));
	}
}