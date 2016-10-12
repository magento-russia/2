<?php
class Df_Core_Model_Format_Date extends Df_Core_Model {
	/** @return Zend_Date */
	public function getDate() {
		return $this->cfg(self::P__DATE);
	}

	/** @return string */
	public function getDayWith2Digits() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_dts($this->getDate(), Zend_Date::DAY);
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function getInRussianFormat() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_dts($this->getDate(), self::FORMAT__RUSSIAN);
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function getMonthInGenetiveCase() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				$this->getDate()->toString(Zend_Date::MONTH_NAME, null, Df_Core_Const::LOCALE__RUSSIAN)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function getYear() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_dts($this->getDate(), Zend_Date::YEAR);
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__DATE, 'Zend_Date');
	}
	const _CLASS = __CLASS__;
	const FORMAT__RUSSIAN = 'dd.MM.yyyy';
	const P__DATE = 'date';
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Core_Model_Format_Date
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}