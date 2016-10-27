<?php
namespace Df\Shipping\Block\Carrier;
use Df\Shipping\Block\Carrier as Carrier;
use Mage_Sales_Model_Quote_Address_Rate as QuoteAddressRate;
/**
 * @method \Df_Checkout_Block_Onepage_Shipping_Method_Available grandParent()
 * @method Carrier parent()
 */
class Rate extends \Df_Core_Block_Abstract_NoCache {
	/**
	 * @override
	 * @see \Mage_Core_Block_Abstract::_toHtml()
	 * @used-by \Mage_Core_Block_Abstract::toHtml()
	 * @return string
	 */
	protected function _toHtml() {
		/** @var string $errorMessage */
		$errorMessage = $this->rate()->getErrorMessage();
		return
			!$errorMessage
			? df_cc_n($this->htmlInput(), $this->htmlLabel())
			: df_tag_list([df_tag_list([$errorMessage])], false, 'messages', 'error-msg')
		;
	}

	/**
	 * @used-by _toHtml()
	 * @return string
	 */
	private function htmlInput() {return
		df_tag_if(df_tag('input', [
			'name' => 'shipping_method'
			,'type' => 'radio'
			,'value' => $this->code()
			,'id' => $this->inputId()
			,'class' => $this->isSole() ? null : 'radio'
			,'checked' => $this->isSelected() ? 'checked' : null
		]), $this->isSole(), 'span', ['class' => 'no-display'])
	;}

	/**
	 * @used-by _toHtml()
	 * @return string
	 */
	private function htmlLabel() {return df_tag('label', ['for' => $this->inputId()], $this->label());}

	/**
	 * @used-by htmlInput()
	 * @used-by inputId()
	 * @used-by isSelected()
	 * @return string
	 */
	private function code() {return $this->rate()->getCode();}

	/**
	 * @used-by htmlInput()
	 * @used-by htmlLabel()
	 * @return string
	 */
	private function inputId() {return 's_method_' . $this->code();}

	/**
	 * @used-by htmlInput()
	 * @return bool
	 */
	private function isSelected() {return
		$this->isSole() || $this->code() === $this->grandParent()->getAddressShippingMethod()
	;}

	/**
	 * @used-by htmlInput()
	 * @return bool
	 */
	private function isSole() {return $this[self::$P__IS_SOLE];}

	/**
	 * @used-by htmlLabel()
	 * @return string
	 */
	private function label() {return Rate\Label::r($this, $this->rate());}

	/**
	 * @used-by code()
	 * @used-by label()
	 * @used-by priceS()
	 * @return \Mage_Sales_Model_Quote_Address_Rate
	 */
	private function rate() {return $this[self::$P__RATE];}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::$P__IS_SOLE, DF_V_BOOL)
			->_prop(self::$P__RATE, QuoteAddressRate::class)
		;
	}
	/** @var string */
	private static $P__IS_SOLE = 'is_sole';
	/** @var string */
	private static $P__RATE = 'rate';

	/**
	 * @used-by \Df\Shipping\Block\Carrier::renderRate()
	 * @param Carrier $parent
	 * @param QuoteAddressRate $rate
	 * @param bool $isSole
	 * @return string
	 */
	public static function r(Carrier $parent, QuoteAddressRate $rate, $isSole) {return
		df_render_child($parent, new self([self::$P__RATE => $rate, self::$P__IS_SOLE => $isSole]));
	}
}