<?php
/**
 * @method Df_Checkout_Block_Onepage_Shipping_Method_Available grandParent()
 * @method Df_Shipping_Block_Carrier parent()
 */
class Df_Shipping_Block_Carrier_Rate extends Df_Core_Block_Abstract_NoCache {
	/**
	 * @override
	 * @see Mage_Core_Block_Abstract::_toHtml()
	 * @used-by Mage_Core_Block_Abstract::toHtml()
	 * @return string
	 */
	protected function _toHtml() {
		/** @var string $errorMessage */
		$errorMessage = $this->rate()->getErrorMessage();
		return
			!$errorMessage
			? df_concat_n($this->htmlInput(), $this->htmlLabel())
			: rm_tag_list(rm_tag_list(array($errorMessage)), false, 'messages', 'error-msg')
		;
	}

	/**
	 * @used-by _toHtml()
	 * @return string
	 */
	private function htmlInput() {
		/** @var string $result */
		$result = rm_tag('input', array(
			'name' => 'shipping_method'
			,'type' => 'radio'
			,'value' => $this->code()
			,'id' => $this->inputId()
			,'class' => $this->isSole() ? null : 'radio'
			,'checked' => $this->isSelected() ? 'checked' : null
		));
		return
			!$this->isSole()
			? $result
			: rm_tag('span', array('class' => 'no-display'), $result)
		;
	}

	/**
	 * @used-by _toHtml()
	 * @return string
	 */
	private function htmlLabel() {
		return rm_tag('label', array('for' => $this->inputId()), $this->label());
	}

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
	private function inputId() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = 's_method_' . $this->code();
		}
		return $this->{__METHOD__};
	}

	/**
	 * @used-by htmlInput()
	 * @return bool
	 */
	private function isSelected() {
		return $this->isSole() || $this->code() === $this->grandParent()->getAddressShippingMethod();
	}

	/**
	 * @used-by htmlInput()
	 * @return bool
	 */
	private function isSole() {return $this[self::$P__IS_SOLE];}

	/**
	 * @used-by htmlLabel()
	 * @return string
	 */
	private function label() {return Df_Shipping_Block_Carrier_Rate_Label::r($this, $this->rate());}

	/**
	 * @used-by code()
	 * @used-by label()
	 * @used-by priceS()
	 * @return Mage_Sales_Model_Quote_Address_Rate
	 */
	private function rate() {return $this[self::$P__RATE];}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::$P__IS_SOLE, RM_V_BOOL)
			->_prop(self::$P__RATE, 'Mage_Sales_Model_Quote_Address_Rate')
		;
	}
	/** @var string */
	private static $P__IS_SOLE = 'is_sole';
	/** @var string */
	private static $P__RATE = 'rate';

	/**
	 * @used-by Df_Shipping_Block_Carrier::renderRate()
	 * @param Df_Shipping_Block_Carrier $parent
	 * @param Mage_Sales_Model_Quote_Address_Rate $rate
	 * @param bool $isSole
	 * @return string
	 */
	public static function r(
		Df_Shipping_Block_Carrier $parent, Mage_Sales_Model_Quote_Address_Rate $rate, $isSole
	) {
		return rm_render_child($parent, new self(array(
			self::$P__RATE => $rate, self::$P__IS_SOLE => $isSole
		)));
	}
}