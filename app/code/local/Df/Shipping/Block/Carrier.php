<?php
namespace Df\Shipping\Block;
use Df_Checkout_Block_Onepage_Shipping_Method_Available as Available;
use Mage_Sales_Model_Quote_Address_Rate as Rate;
/** @method Available parent() */
class Carrier extends \Df_Core_Block_Abstract_NoCache {
	/**
	 * @override
	 * @see Mage_Core_Block_Abstract::_toHtml()
	 * @used-by Mage_Core_Block_Abstract::toHtml()
	 * @return string
	 */
	protected function _toHtml() {return df_cc_n(
		df_tag('dt', [], df_e($this->parent()->getCarrierName($this[self::$P__CODE])))
		,df_tag('dd', [], df_tag_list(array_map(function(Rate $rate) {return
			Carrier\Rate::r($this, $rate, $this->isSole())
		;}, $this[self::$P__RATES])))
	);}

	/**
	 * @used-by _toHtml()
	 * @return bool
	 */
	private function isSole() {return dfc($this, function() {
		/** @noinspection PhpParamsInspection */ return
		1 === count($this->parent()->getShippingRates()) && 1 === count($this[self::$P__RATES])
	;});}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::$P__CODE, DF_V_STRING_NE)
			->_prop(self::$P__RATES, DF_V_ARRAY)
		;
	}
	/** @var string */
	private static $P__CODE = 'code';
	/** @var string */
	private static $P__RATES = 'rates';

	/**
	 * @used-by Available::renderCarriers()
	 * @param Available $parent
	 * @param string $code
	 * @param Rate[] $rates
	 * @return string
	 */
	public static function r(Available $parent, $code, array $rates) {return
		df_render_child($parent, new self([self::$P__CODE => $code, self::$P__RATES => $rates]));
	}
}