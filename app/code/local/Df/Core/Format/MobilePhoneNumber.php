<?php
class Df_Core_Format_MobilePhoneNumber extends Df_Core_Model {
	/** @return string */
	public function getOnlyDigits() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = preg_replace('#[^\d]#u', '', $this->getValue());
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function getOnlyDigitsWithoutCallingCode() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				preg_replace(
					df_sprintf(
						'#^(%s)#'
						,df_ccc('|'
							,$this->getCallingCode()
							,$this->getAlternativeCallingCode()
						)
					)
					,''
					,$this->getOnlyDigits()
				)
			;
		}
		return $this->{__METHOD__};
	}
	/** @var string */
	private $_onlyDigitsWithoutCallingCode;

	/** @return bool */
	public function isValid() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				df_check_between(mb_strlen($this->getOnlyDigits()), self::MIN_LENGTH, self::MAX_LENGTH)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getAlternativeCallingCode() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				is_null($this->getCountry())
				? ''
				: df_h()->directory()->finder()->callingCode()
					->getAlternativeByCountry($this->getCountry())
			;
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getCallingCode() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				is_null($this->getCountry())
				? ''
				: df_h()->directory()->finder()->callingCode()
					->getByCountry($this->getCountry())
			;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Directory_Model_Country|null */
	private function getCountry() {
		/** @var Df_Directory_Model_Country|null $result */
		$result = $this->cfg(self::P__COUNTRY);
		// Не знаю, насколько хорошо данное архитектурное решение.
		// Практика покажет.
		return $result ? $result : df_h()->directory()->country()->getRussia();
	}

	/** @return string */
	private function getValue() {return $this->cfg(self::P__VALUE, '');}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__VALUE,	DF_V_STRING, false)
			->_prop(self::P__COUNTRY, Df_Directory_Model_Country::class, false)
		;
	}

	const MAX_LENGTH = 15;
	const MIN_LENGTH = 11;
	const P__COUNTRY = 'country';
	const P__VALUE = 'value';
	/**
	 * @param Df_Sales_Model_Quote_Address|Mage_Sales_Model_Quote_Address $address
	 * @return Df_Core_Format_MobilePhoneNumber
	 */
	public static function fromQuoteAddress(Df_Sales_Model_Quote_Address $address) {
		return self::i($address->getTelephone());
	}
	/** @return string[] */
	public static function getCssClasses() {
		if (!isset(self::$_cssClasses)) {
			self::$_cssClasses = array(
				'validate-digits'
				,'validate-length'
				,'minimum-length-' . self::MIN_LENGTH
				,'maximum-length-' . self::MAX_LENGTH
			);
		}
		return self::$_cssClasses;
	}
	/** @var string[] */
	private static $_cssClasses;
	/**
	 * @static
	 * @param string|null $value
	 * @param Df_Directory_Model_Country|null $country [optional]
	 * @return Df_Core_Format_MobilePhoneNumber
	 */
	public static function i($value = '', $country = null) {
		return new self(array(self::P__VALUE => $value, self::P__COUNTRY => $country));
	}
}