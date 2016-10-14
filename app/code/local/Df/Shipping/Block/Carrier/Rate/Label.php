<?php
/**
 * @method Df_Checkout_Block_Onepage_Shipping_Method_Available grandGrandParent()
 * @method Df_Shipping_Block_Carrier_Rate parent()
 */
class Df_Shipping_Block_Carrier_Rate_Label extends Df_Core_Block_Abstract_NoCache {
	/**
	 * @override
	 * @see Mage_Core_Block_Abstract::_toHtml()
	 * @used-by Mage_Core_Block_Abstract::toHtml()
	 * @return string
	 */
	protected function _toHtml() {
		/** @var string $result */
		$result = rm_concat_clean(': ', rm_e($this->rate()->getMethodTitle()), $this->priceS());
		/** @var string $dateS */
		$dateS = $this->dateS();
		if ($dateS) {
			/**
			 * 2015-04-09
			 * Форматируем срок доставки посредством класса CSS «.price»,
			 * чтобы он выглядел стилистически идентично стоимости доставки.
			 */
			$result .= ',  ' . rm_tag('span', array('class' => 'price'), $dateS);
		}
		return $result;
	}

	/**
	 * @used-by _toHtml()
	 * @return string
	 */
	private function dateS() {
		/** @var string $result */
		/** @var Df_Shipping_Rate_Result_Method|null $terms */
		$terms = $this->rate()->terms();
		if ($terms) {
			$result = rm_days_interval(rm_days_till($terms->dateMin()), rm_days_till($terms->dateMax()));
		}
		return isset($result) ? $result : '';
	}

	/**
	 * 2015-04-21
	 * В отличие от Magento Community Edition, у нас стоимость доставки всегда показывается конечная
	 * (с НДС, если услуга доставка облагается НДС), потому что так принято в России и СНГ.
	 * По сути, значения административных настроек игнорируются.
	 * @used-by label()
	 * @return string
	 */
	private function priceS() {
		return $this->grandGrandParent()->getShippingPrice($this->rate()->getPrice(), $withTax = true);
	}

	/**
	 * @used-by _toHtml()
	 * @used-by priceS()
	 * @return Mage_Sales_Model_Quote_Address_Rate|Df_Sales_Model_Quote_Address_Rate
	 */
	private function rate() {return $this[self::$P__RATE];}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__RATE, 'Mage_Sales_Model_Quote_Address_Rate');
	}
	/** @var string */
	private static $P__RATE = 'rate';

	/**
	 * @used-by Df_Shipping_Block_Carrier_Rate::label()
	 * @param Df_Shipping_Block_Carrier_Rate $parent
	 * @param Mage_Sales_Model_Quote_Address_Rate $rate
	 * @return string
	 */
	public static function r(
		Df_Shipping_Block_Carrier_Rate $parent, Mage_Sales_Model_Quote_Address_Rate $rate
	) {
		return rm_render_child($parent, new self(array(self::$P__RATE => $rate)));
	}
}