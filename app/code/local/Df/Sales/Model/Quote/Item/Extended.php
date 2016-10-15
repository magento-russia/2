<?php
class Df_Sales_Model_Quote_Item_Extended extends Df_Core_Model {
	/** @return float */
	public function getBaseCost() {return $this->_getF('base_cost');}
	/** @return float */
	public function getBaseDiscountAmount() {return $this->_getF('base_discount_amount');}
	/** @return float */
	public function getBaseHiddenTaxAmount() {return $this->_getF('base_hidden_tax_amount');}
	/** @return float */
	public function getBasePrice() {return $this->_getF('base_price');}
	/** @return float */
	public function getBasePriceInclTax() {return $this->_getF('base_price_incl_tax');}
	/** @return float */
	public function getBaseRowTotal() {return $this->_getF('base_row_total');}
	/** @return float */
	public function getBaseRowTotalInclTax() {return $this->_getF('base_row_total_incl_tax');}
	/** @return float */
	public function getBaseTaxAmount() {return $this->_getF('base_tax_amount');}
	/** @return float */
	public function getBaseTaxBeforeDiscount() {return $this->_getF('base_tax_before_discount');}
	/** @return float */
	public function getBaseWeeeTaxAppliedAmount() {return $this->_getF('base_weee_tax_applied_amount');}
	/** @return float */
	public function getBaseWeeeTaxAppliedRowAmount() {return $this->_getF('base_weee_tax_applied_row_amnt');}
	/** @return float */
	public function getBaseWeeeTaxDisposition() {return $this->_getF('base_weee_tax_disposition');}
	/** @return float */
	public function getBaseWeeeTaxRowDisposition() {return $this->_getF('base_weee_tax_row_disposition');}
	/** @return float */
	public function getCustomPrice() {return $this->_getF('custom_price');}
	/** @return float */
	public function getDiscountAmount() {return $this->_getF('discount_amount');}
	/** @return float */
	public function getDiscountPercent() {return $this->_getF('discount_percent');}
	/** @return float */
	public function getHiddenTaxAmount() {return $this->_getF('hidden_tax_amount');}
	/** @return float */
	public function getOriginalCustomPrice() {return $this->_getF('original_custom_price');}
	/** @return float */
	public function getPrice() {return $this->_getF('price');}
	/** @return float */
	public function getPriceInclTax() {return $this->_getF('price_incl_tax');}
	/** @return float */
	public function getQty() {return $this->_getF('qty');}
	/** @return float */
	public function getRowTotal() {return $this->_getF('row_total');}
	/** @return float */
	public function getRowTotalInclTax() {return $this->_getF('row_total_incl_tax');}
	/** @return float */
	public function getRowTotalWithDiscount() {return $this->_getF('row_total_with_discount');}
	/** @return float */
	public function getRowWeight() {return $this->_getF('row_weight');}
	/** @return float */
	public function getTaxAmount() {return $this->_getF('tax_amount');}
	/** @return float */
	public function getTaxBeforeDiscount() {return $this->_getF('tax_before_discount');}
	/** @return float */
	public function getTaxPercent() {return $this->_getF('tax_percent');}
	/** @return float */
	public function getWeeeTaxApplied() {return $this->_getF('weee_tax_applied');}
	/** @return float */
	public function getWeeeTaxAppliedAmount() {return $this->_getF('weee_tax_applied_amount');}
	/** @return float */
	public function getWeeeTaxAppliedRowAmount() {return $this->_getF('weee_tax_applied_row_amount');}
	/** @return float */
	public function getWeeeTaxRowDisposition() {return $this->_getF('weee_tax_row_disposition');}
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
			: $this->getQuoteItem()->getDataUsingMethod($key)
		;
	}

	/**
	 * @param string $key
	 * @return float
	 */
	private function _getF($key) {return rm_float($this->_get($key));}

	/** @return Mage_Sales_Model_Quote_Item */
	private function getQuoteItem() {return $this->cfg(self::P__QUOTE_ITEM);}

	/** @return Mage_Sales_Model_Order_Item|null */
	private function getParent() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_n_set($this->getQuoteItem()->getParentItem());
		}
		return df_n_get($this->{__METHOD__});
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__QUOTE_ITEM, 'Mage_Sales_Model_Quote_Item');
	}
	const _C = __CLASS__;
	const P__QUOTE_ITEM = 'quote_item';
	/**
	 * @static
	 * @param Mage_Sales_Model_Quote_Item $quoteItem
	 * @return Df_Sales_Model_Quote_Item_Extended
	 */
	public static function i(Mage_Sales_Model_Quote_Item $quoteItem) {
		return new self(array(self::P__QUOTE_ITEM => $quoteItem));
	}
}