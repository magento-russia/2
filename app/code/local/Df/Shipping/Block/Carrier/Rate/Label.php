<?php
namespace Df\Shipping\Block\Carrier\Rate;
use Df\Shipping\Block\Carrier\Rate as RateBlock;
use Mage_Sales_Model_Quote_Address_Rate as Rate;
/**
 * @method \Df_Checkout_Block_Onepage_Shipping_Method_Available grandGrandParent()
 * @method RateBlock parent()
 */
class Label extends \Df_Core_Block_Abstract_NoCache {
	/**
	 * @override
	 * @see \Mage_Core_Block_Abstract::_toHtml()
	 * @used-by \Mage_Core_Block_Abstract::toHtml()
	 * @return string
	 */
	protected function _toHtml() {
		/** @var string $result */
		$result = df_ccc(': ', df_e($this->rate()->getMethodTitle()), $this->priceS());
		/** @var string $dateS */
		$dateS = $this->dateS();
		if ($dateS) {
			/**
			 * 2015-04-09
			 * Форматируем срок доставки посредством класса CSS «.price»,
			 * чтобы он выглядел стилистически идентично стоимости доставки.
			 */
			$result .= ',  ' . df_tag('span', array('class' => 'price'), $dateS);
		}
		return $result;
	}

	/**
	 * @used-by _toHtml()
	 * @return string
	 */
	private function dateS() {
		/** @var \Df\Shipping\Rate\Result\Method|null $t */
		$t = $this->rate()->terms();
		return !$t ? '' : df_days_interval(df_days_left($t->dateMin()), df_days_left($t->dateMax()));
	}

	/**
	 * 2015-04-21
	 * В отличие от Magento Community Edition, у нас стоимость доставки всегда показывается конечная
	 * (с НДС, если услуга доставка облагается НДС), потому что так принято в России и СНГ.
	 * По сути, значения административных настроек игнорируются.
	 * @used-by label()
	 * @return string
	 */
	private function priceS() {return
		$this->grandGrandParent()->getShippingPrice($this->rate()->getPrice(), $withTax = true)
	;}

	/**
	 * @used-by _toHtml()
	 * @used-by priceS()
	 * @return \Mage_Sales_Model_Quote_Address_Rate|\Df_Sales_Model_Quote_Address_Rate
	 */
	private function rate() {return $this[self::$P__RATE];}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__RATE, Rate::class);
	}
	/** @var string */
	private static $P__RATE = 'rate';

	/**
	 * @used-by \Df\Shipping\Block\Carrier\Rate::label()
	 * @param RateBlock $parent
	 * @param Rate $rate
	 * @return string
	 */
	public static function r(RateBlock $parent, Rate $rate) {return
		df_render_child($parent, new self([self::$P__RATE => $rate]));
	}
}