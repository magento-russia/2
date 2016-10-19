<?php
use Df_Accounting_Settings_Vat as VAT;
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
	protected function _toHtml() {return
		$this->needShowTax()
		? df_cc_n(
			$this->row('Grand Total Incl. Tax', $this->getTotal()->getValue())
			,$this->singleTax() ? $this->rowVAT() : $this->renderTotals('taxes', $this->getColspan())
		)
		: $this->row($this->getTotal()->getTitle(), $this->getTotal()->getValue())
	;}

	/**
	 * @used-by renderCell_name()
	 * @used-by renderCell_value()
	 * @param string $value
	 * @param bool $useColspan [optional]
	 * @param int $colspanAdd [optional]
	 * @return string
	 */
	private function cell($value, $useColspan = false, $colspanAdd = 0) {return
		df_tag('td', [
			'class' => 'a-right'
			,'colspan' => $useColspan ? $this->getColspan() + $colspanAdd : null
			, 'style' => $this->getStyle()
		], df_tag('strong', [], $value));
	}

	/**
	 * @used-by row()
	 * @param string $name
	 * @return string
	 */
	private function cell_name($name) {return
		$this->cell(df_mage()->taxHelper()->__($name), $useColspan = true)
	;}

	/**
	 * @used-by row()
	 * @param float $value
	 * @return string
	 */
	private function cell_value($value) {return
		$this->cell(df_mage()->checkoutHelper()->formatPrice($value))
	;}

	/**
	 * 2016-10-19
	 * @return bool
	 */
	private function needShowTax() {return
		parent::includeTax()
		&& $this->getTotalExclTax() >= 0
		&& (
			// 2016-18-19
			// Если налогов несколько (не только НДС)
			!$this->singleTax()
			// 2016-18-19
			// Если магазин является плательщиком НДС внутри страны и хочет показывать НДС.
			|| VAT::s()->enabled() && VAT::s()->show()
			// 2016-18-19
			// Если налог только один (НДС), имеет нулевую ставку либо не применяется
			// и покупатель явно хочет это показать.
			|| $this->needShowZeroVAT()
		)
	;}

	/**
	 * 2016-10-19
	 * @usec-by needShowTax()
	 * @return bool
	 */
	private function needShowZeroVAT() {return dfc($this, function() {return
		$this->singleTax()
		&& !$this->singleTax()->getValue()
		&& df_tax_c()->displayCartZeroTax($this->getStore())
	;});}

	/**
	 * @used-by _toHtml()
	 * @param string $name
	 * @param float $value
	 * @param string|null $class [optional]
	 * @return string
	 */
	private function row($name, $value, $class = null) {return
		df_tag('tr', ['class' => $class], df_cc_n($this->cell_name($name), $this->cell_value($value)))
	;}

	/**
	 * @used-by _toHtml()
	 * @return string
	 */
	private function rowVAT() {return
		!$this->needShowZeroVAT()
		? $this->row('Including VAT', $this->singleTax()->getValue(), 'df-vat')
		: df_tag('tr', ['class' => 'df-vat'], $this->cell(df_mage()->taxHelper()->__('No VAT'), true, 1))
	;}

	/** @return Df_Sales_Model_Quote_Address_Total|Mage_Sales_Model_Quote_Address_Total|null */
	private function singleTax() {return dfc($this, function() {return
		1 !== count($this->taxTotals()) ? null : df_first($this->taxTotals())
	;});}

	/**
	 * @used-by getTemplate()
	 * @param $fileName
	 * @return string
	 */
	private function t($fileName) {return "df/tax/checkout/grandtotal/{$fileName}.phtml";}

	/** @return array(Df_Sales_Model_Quote_Address_Total|Mage_Sales_Model_Quote_Address_Total) */
	private function taxTotals() {return dfc($this, function() {return
		array_filter($this->getTotals(), function($total) {
			/** @var Df_Sales_Model_Quote_Address_Total|Mage_Sales_Model_Quote_Address_Total $total */
			return 'taxes' === $total->getArea();
		})
	;});}
}