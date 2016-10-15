<?php
/**
 * @method int|null getColspan()
 * @see Mage_Checkout_Block_Cart_Totals::renderTotal():
 * ->setColspan($colspan)
 * https://github.com/OpenMage/magento-mirror/blob/1.9.2.1/app/code/core/Mage/Checkout/Block/Cart/Totals.php#L75
 *
 * @method Mage_Sales_Model_Quote_Address_Total|Df_Sales_Model_Quote_Address_Total getTotal()
 * @see Mage_Checkout_Block_Cart_Totals::renderTotal():
 * ->setTotal($total)
 * https://github.com/OpenMage/magento-mirror/blob/1.9.2.1/app/code/core/Mage/Checkout/Block/Cart/Totals.php#L74
 *
 * 2015-08-08
 * Перекрываем родительский класс
 * ради адаптированного для России отображения налогов покупателю.
 *
 * Обратите внимание, что этот класс перекрывает родительский класс не посредством rewrite,
 * а посредством настройки в ветке global/sales/quote/totals/{$code}/renderer:
 * https://github.com/OpenMage/magento-mirror/blob/magento-1.9.2.1/app/code/core/Mage/Checkout/Block/Cart/Totals.php#L53
 * https://github.com/OpenMage/magento-mirror/blob/1.9.2.1/app/code/core/Mage/Tax/etc/config.xml#L193
 * @used-by Mage_Checkout_Block_Cart_Totals::_getTotalRenderer()
 */
class Df_Tax_Block_Checkout_Grandtotal extends Mage_Tax_Block_Checkout_Grandtotal {
	/**
	 * 2015-08-08
	 * Перекрываем ради адаптированного для России отображения налогов налогов покупателю.
	 * Обратите внимание, что оформительские темы этот шаблон не перекрывают,
	 * потому что данный блок формируется программно:
	 * @see Mage_Checkout_Block_Cart_Totals::_getTotalRenderer()
	 * https://github.com/OpenMage/magento-mirror/blob/magento-1.9.2.1/app/code/core/Mage/Checkout/Block/Cart/Totals.php#L53
	 * @override
	 * @see Mage_Core_Block_Template::_toHtml()
	 * @return string
	 */
	protected function _toHtml() {
		return
			$this->includeTax() && $this->getTotalExclTax() >= 0
			? df_cc_n(
				//$this->row('Grand Total Excl. Tax', $this->getTotalExclTax())
				$this->row('Grand Total Incl. Tax', $this->getTotal()->getValue())
				,$this->singleTax() ? $this->rowVAT() : $this->renderTotals('taxes', $this->getColspan())
			)
			: $this->row($this->getTotal()->getTitle(), $this->getTotal()->getValue())
		;
	}

	/**
	 * @used-by renderCell_name()
	 * @used-by renderCell_value()
	 * @param string $value
	 * @param bool $useColspan [optional]
	 * @return string
	 */
	private function cell($value, $useColspan = false) {
		return df_tag('td', array(
			'class' => 'a-right'
			,'colspan' => $useColspan ? $this->getColspan() : null
			, 'style' => $this->getStyle()
		), df_tag('strong', array(), $value));
	}

	/**
	 * @used-by row()
	 * @param string $name
	 * @return string
	 */
	private function cell_name($name) {
		return $this->cell(df_mage()->taxHelper()->__($name), $useColspan = true);
	}

	/**
	 * @used-by row()
	 * @param float $value
	 * @return string
	 */
	private function cell_value($value) {
		return $this->cell(df_mage()->checkoutHelper()->formatPrice($value), $useColspan = false);
	}

	/**
	 * @used-by _toHtml()
	 * @param string $name
	 * @param float $value
	 * @param string|null $class [optional]
	 * @return string
	 */
	private function row($name, $value, $class = null) {
		return df_tag(
			'tr', array('class' => $class)
			, df_cc_n($this->cell_name($name)
			, $this->cell_value($value))
		);
	}

	/**
	 * @used-by _toHtml()
	 * @return string
	 */
	private function rowVAT() {
		return $this->row('Including VAT', $this->singleTax()->getValue(), 'df-vat');
	}

	/** @return Df_Sales_Model_Quote_Address_Total|Mage_Sales_Model_Quote_Address_Total|null */
	private function singleTax() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_n_set(
				1 !== count($this->taxTotals()) ? null : df_first($this->taxTotals())
			);
		}
		return df_n_get($this->{__METHOD__});
	}

	/**
	 * @used-by getTemplate()
	 * @param $fileName
	 * @return string
	 */
	private function t($fileName) {return "df/tax/checkout/grandtotal/{$fileName}.phtml";}

	/** @return array(Df_Sales_Model_Quote_Address_Total|Mage_Sales_Model_Quote_Address_Total) */
	private function taxTotals() {
		if (!isset($this->{__METHOD__})) {
			/** @var array(Df_Sales_Model_Quote_Address_Total|Mage_Sales_Model_Quote_Address_Total) $result */
			$result = array();
			foreach ($this->getTotals() as $total) {
				/** @var Df_Sales_Model_Quote_Address_Total|Mage_Sales_Model_Quote_Address_Total $total */
				if ('taxes' === $total->getArea()) {
					$result[]= $total;
				}
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}
}