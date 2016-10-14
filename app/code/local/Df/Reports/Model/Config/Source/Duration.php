<?php
/**
 * @singleton
 * Система создаёт объект-одиночку для потомков этого класса.
 * Не забывайте об этом при реализации кеширования результатов вычислений внутри этого класса!
 */
class Df_Reports_Model_Config_Source_Duration extends Df_Admin_Config_Source {
	/**
	 * @override
	 * @param bool $isMultiSelect
	 * @return mixed[][]
	 */
	protected function toOptionArrayInternal($isMultiSelect = false) {
		return array(
			$this->option(self::UNDEFINED, 'не устанавливать')
			,$this->option(self::ONE_DAY, '1 день', Zend_Date::DAY_SHORT, 1)
			,$this->option(self::ONE_WEEK, '1 неделя', Zend_Date::DAY_SHORT, 7)
			,$this->option(self::ONE_MONTH, '1 месяц', Zend_Date::MONTH_SHORT, 1)
		);
	}

	/**
	 * @param string $value
	 * @param string $label
	 * @param string|null $durationDatePart [optional]
	 * @param int|null $durationValue [optional]
	 * @return array(string => string|array(string|int))
	 */
	private function option($value, $label, $durationDatePart = null, $durationValue = null) {
		/** @var array(string => string|array(string|int)) $result */
		$result = rm_option($value, $label);
		if ($durationDatePart) {
			$result += array(self::OPTION_PARAM__DURATION => array(
				self::OPTION_PARAM__DURATION__DATEPART => $durationDatePart
				,self::OPTION_PARAM__DURATION__VALUE => $durationValue
			));
		}
		return $result;
	}

	const _C = __CLASS__;
	const OPTION_PARAM__DURATION = 'duration';
	const OPTION_PARAM__DURATION__DATEPART = 'datePart';
	const OPTION_PARAM__DURATION__VALUE = 'value';
	const ONE_DAY = 'one_day';
	const ONE_MONTH = 'one_month';
	const ONE_QUARTER = 'one_quarter';
	const ONE_WEEK = 'one_week';
	const ONE_YEAR = 'one_year';
	const UNDEFINED = 'undefined';
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Reports_Model_Config_Source_Duration
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}