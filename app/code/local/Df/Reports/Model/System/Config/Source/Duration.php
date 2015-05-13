<?php
/**
 * @singleton
 * Система создаёт объект-одиночку для потомков этого класса.
 * Не забывайте об этом при реализации кеширования результатов вычислений внутри этого класса!
 */
class Df_Reports_Model_System_Config_Source_Duration extends Df_Admin_Model_Config_Source {
	/**
	 * @override
	 * @param bool $isMultiSelect
	 * @return mixed[][]
	 */
	protected function toOptionArrayInternal($isMultiSelect = false) {
		return
			array(
				array(
					self::OPTION_KEY__LABEL => 'не устанавливать'
					,self::OPTION_KEY__VALUE => self::UNDEFINED
				)
				,array(
					self::OPTION_KEY__LABEL => '1 день'
					,self::OPTION_KEY__VALUE => self::ONE_DAY
					,self::OPTION_PARAM__DURATION =>
						array(
							self::OPTION_PARAM__DURATION__DATEPART => Zend_Date::DAY_SHORT
							,self::OPTION_PARAM__DURATION__VALUE => 1
						)
				)
				,array(
					self::OPTION_KEY__LABEL => '1 неделя'
					,self::OPTION_KEY__VALUE => self::ONE_WEEK
					,self::OPTION_PARAM__DURATION =>
						array(
							self::OPTION_PARAM__DURATION__DATEPART => Zend_Date::DAY_SHORT
							,self::OPTION_PARAM__DURATION__VALUE => 7
						)
				)
				,array(
					self::OPTION_KEY__LABEL => '1 месяц'
					,self::OPTION_KEY__VALUE => self::ONE_MONTH
					,self::OPTION_PARAM__DURATION =>
						array(
							self::OPTION_PARAM__DURATION__DATEPART => Zend_Date::MONTH_SHORT
							,self::OPTION_PARAM__DURATION__VALUE => 1
						)
				)
				,array(
					self::OPTION_KEY__LABEL => '1 квартал'
					,self::OPTION_KEY__VALUE => self::ONE_QUARTER
					,self::OPTION_PARAM__DURATION =>
						array(
							self::OPTION_PARAM__DURATION__DATEPART => Zend_Date::MONTH_SHORT
							,self::OPTION_PARAM__DURATION__VALUE => 3
						)
				)
				,array(
					self::OPTION_KEY__LABEL => '1 год'
					,self::OPTION_KEY__VALUE => self::ONE_YEAR
					,self::OPTION_PARAM__DURATION =>
						array(
							self::OPTION_PARAM__DURATION__DATEPART => Zend_Date::YEAR
							,self::OPTION_PARAM__DURATION__VALUE => 1
						)
				)
			)
		;
	}
	const _CLASS = __CLASS__;
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
	 * @return Df_Reports_Model_System_Config_Source_Duration
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}