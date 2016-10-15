<?php
class Df_Core_Model_Css_Rule extends Df_Core_Model {
	/**
	 * @used-by compose()
	 * @used-by Df_Core_Model_Css_Rule_Set::getText()
	 * @return string
	 */
	public function getText() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = "{$this->getName()}: {$this->getValue()}{$this->getUnits()};";
		}
		return $this->{__METHOD__};
	}

	/**
	 * @used-by getText()
	 * @return string
	 */
	private function getName() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $result */
			$result = $this->cfg(self::$P__NAME);
			$this->{__METHOD__} = !is_array($result) ? $result : implode('-', $result);
		}
		return $this->{__METHOD__};
	}

	/**
	 * @used-by getText()
	 * @return string
	 */
	private function getUnits() {return $this->cfg(self::$P__UNITS);}

	/**
	 * @used-by getText()
	 * @return string
	 */
	private function getValue() {return $this->cfg(self::$P__VALUE);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::$P__NAME, DF_V_STRING_NE)
			->_prop(self::$P__UNITS, DF_V_STRING)
			->_prop(self::$P__VALUE, DF_V_STRING_NE)
		;
	}
	/** @used-by Df_Core_Model_Css_Rule_Set::itemClass() */

	/** @var string */
	private static $P__NAME = 'name';
	/** @var string */
	private static $P__UNITS = 'units';
	/** @var string */
	private static $P__VALUE = 'value';

	/**
	 * @used-by Df_Checkout_Block_Frontend_Review_OrderComments::getFloatRule()
	 * @used-by Df_Checkout_Block_Frontend_Review_OrderComments::getMarginRule()
	 * @used-by Df_Core_Block_FormattedText::getCssRules()
	 * @param string|string[] $propertyName
	 * @param string|int|float $propertyValue
	 * @param string|null $propertyUnits [optional]
	 * @param bool $appendLineEnding [optional]
	 * @return string
	 */
	public static function compose(
		$propertyName, $propertyValue, $propertyUnits = null, $appendLineEnding = true
	) {
		/** @var string $result */
		$result = self::i($propertyName, $propertyValue, $propertyUnits)->getText();
		if ($appendLineEnding) {
			$result .= "\n";
		}
		return $result;
	}

	/**
	 * @used-by compose()
	 * @used-by Df_Core_Model_Css_Rule_Set::addRule()
	 * @param string|string[] $name
	 * @param string $value
	 * @param string|null $units [optional]
	 * @return Df_Core_Model_Css_Rule
	 */
	public static function i($name, $value, $units = null) {
		return new self(array(
			self::$P__NAME => $name, self::$P__VALUE => $value, self::$P__UNITS => $units
		));
	}
}