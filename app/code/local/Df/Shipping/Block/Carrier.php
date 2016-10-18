<?php
/** @method Df_Checkout_Block_Onepage_Shipping_Method_Available parent() */
class Df_Shipping_Block_Carrier extends Df_Core_Block_Abstract_NoCache {
	/**
	 * @override
	 * @see Mage_Core_Block_Abstract::_toHtml()
	 * @used-by Mage_Core_Block_Abstract::toHtml()
	 * @return string
	 */
	protected function _toHtml() {
		return df_cc_n(
			df_tag('dt', array(), df_e($this->parent()->getCarrierName($this[self::$P__CODE])))
			/** @uses renderRate() */
			,df_tag('dd', array(), df_tag_list(array_map(array($this, 'renderRate'), $this->rates())))
		);
	}

	/**
	 * @used-by renderRate()
	 * @return bool
	 */
	private function isSole() {
		if (!isset($this->{__METHOD__})) {
			/** @noinspection PhpParamsInspection */
			$this->{__METHOD__} =
				1 === count($this->parent()->getShippingRates()) && 1 === count($this->rates())
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @used-by _toHtml()
	 * @used-by isSole()
	 * @return array(mixed => mixed)
	 */
	private function rates() {return $this[self::$P__RATES];}

	/**
	 * @used-by array_map()
	 * @uses-by _toHtml()
	 * @param Mage_Sales_Model_Quote_Address_Rate $rate
	 * @return string
	 */
	private function renderRate(Mage_Sales_Model_Quote_Address_Rate $rate) {
		return Df_Shipping_Block_Carrier_Rate::r($this, $rate, $this->isSole());
	}

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
	 * @used-by Df_Checkout_Block_Onepage_Shipping_Method_Available::renderCarriers()
	 * @param Df_Checkout_Block_Onepage_Shipping_Method_Available $parent
	 * @param string $code
	 * @param Mage_Sales_Model_Quote_Address_Rate[] $rates
	 * @return string
	 */
	public static function r(
		Df_Checkout_Block_Onepage_Shipping_Method_Available $parent, $code, array $rates
	) {
		return df_render_child($parent, new self(array(
			self::$P__CODE => $code, self::$P__RATES => $rates
		)));
	}
}