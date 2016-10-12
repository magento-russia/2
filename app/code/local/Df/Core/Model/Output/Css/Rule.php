<?php
class Df_Core_Model_Output_Css_Rule extends Df_Core_Model {
	/** @return string */
	public function __toString() {return $this->getText(true);}

	/** @return string */
	public function getName() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $result */
			$result = $this->cfg(self::P__NAME);
			$this->{__METHOD__} = !is_array($result) ? $result : implode('-', $result);
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param bool $inline[optional]
	 * @return string
	 */
	public function getText($inline = true) {
		df_param_boolean($inline, 0);
		if (!isset($this->{__METHOD__}[$inline])) {
			/** @var string $result */
			$result =
				df_concat(
					implode(
						': '
						,array(
							$this->getName()
							,df_concat($this->getValue(), $this->getUnits())
						)
					)
					,';'
				)
			;
			if (!$inline) {
				$result .= "\r\n";
			}
			$this->{__METHOD__}[$inline] = $result;
		}
		return $this->{__METHOD__}[$inline];
	}

	/** @return string */
	public function getUnits() {return $this->cfg(self::P__UNITS);}

	/** @return string */
	public function getValue() {return $this->cfg(self::P__VALUE);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__UNITS, self::V_STRING)
			->_prop(self::P__VALUE, self::V_STRING_NE)
		;
	}
	const _CLASS = __CLASS__;
	const P__NAME = 'name';
	const P__UNITS = 'units';
	const P__VALUE = 'value';
	/**
	 * @static
	 * @param string|array $propertyName
	 * @param string|int|float $propertyValue
	 * @param string|null $propertyUnits[optional]
	 * @param bool $appendLineEnding[optional]
	 * @return string
	 */
	public static function compose(
		$propertyName, $propertyValue, $propertyUnits = null, $appendLineEnding = true
	) {
		return self::i($propertyName, $propertyValue, $propertyUnits)->getText(!$appendLineEnding);
	}
	/**
	 * @static
	 * @param string $name
	 * @param string $value
	 * @param string|null $units [optional]
	 * @return Df_Core_Model_Output_Css_Rule
	 */
	public static function i($name, $value, $units = null) {
		return new self(array(
			self::P__NAME => $name, self::P__VALUE => $value, self::P__UNITS => $units
		));
	}
}