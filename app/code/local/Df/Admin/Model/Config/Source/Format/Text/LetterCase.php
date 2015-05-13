<?php
/**
 * @singleton
 * Система создаёт объект-одиночку для потомков этого класса.
 * Не забывайте об этом при реализации кеширования результатов вычислений внутри этого класса!
 */
class Df_Admin_Model_Config_Source_Format_Text_LetterCase extends Df_Admin_Model_Config_Source {
	/**
	 * @override
	 * @param bool $isMultiSelect
	 * @return array(array(string => string))
	 */
	protected function toOptionArrayInternal($isMultiSelect = false) {
		return
			array(
				array(
					self::OPTION_KEY__LABEL => 'не менять'
					,self::OPTION_KEY__VALUE => self::_DEFAULT
				)
				,array(
					self::OPTION_KEY__LABEL => 'с заглавной буквы'
					,self::OPTION_KEY__VALUE => self::UCFIRST
				)
				,array(
					self::OPTION_KEY__LABEL => 'заглавными буквами'
					,self::OPTION_KEY__VALUE => self::UPPERCASE
				)
				,array(
					self::OPTION_KEY__LABEL => 'строчными буквами'
					,self::OPTION_KEY__VALUE => self::LOWERCASE
				)
			)
		;
	}

	/**
	 * @static
	 * @param string $value
	 * @return string
	 */
	public static function convertToCss($value) {
		/** @var string $result */
		$result =
			df_a(
				array(
					self::_DEFAULT => 'none'
					,self::UPPERCASE => self::UPPERCASE
					,self::LOWERCASE => self::LOWERCASE
					,self::UCFIRST => 'capitalize'
				)
				,$value
			)
		;
		df_result_string_not_empty($result);
		return $result;
	}
	const _CLASS = __CLASS__;
	const _DEFAULT = 'default';
	const LOWERCASE = 'lowercase';
	const UCFIRST = 'ucfirst';
	const UPPERCASE = 'uppercase';

	/** @return Df_Admin_Model_Config_Source_Format_Text_LetterCase */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}