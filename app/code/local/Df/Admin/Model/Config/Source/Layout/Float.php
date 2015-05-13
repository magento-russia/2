<?php
/**
 * @singleton
 * Система создаёт объект-одиночку для потомков этого класса.
 * Не забывайте об этом при реализации кеширования результатов вычислений внутри этого класса!
 */
class Df_Admin_Model_Config_Source_Layout_Float extends Df_Admin_Model_Config_Source {
	/**
	 * @override
	 * @param bool $isMultiSelect
	 * @return array(array(string => string))
	 */
	protected function toOptionArrayInternal($isMultiSelect = false) {
		return
			array(
				array(
					self::OPTION_KEY__LABEL => 'нет'
					,self::OPTION_KEY__VALUE => self::NONE
				)
				,array(
					self::OPTION_KEY__LABEL =>
						'прислонить к левому краю родительского блока (обтекание справо)'
					,self::OPTION_KEY__VALUE => self::LEFT
				)
				,array(
					self::OPTION_KEY__LABEL =>
						'прислонить к правому краю родительского блока (обтекание слева)'
					,self::OPTION_KEY__VALUE => self::RIGHT
				)
			)
		;
	}
	const _CLASS = __CLASS__;
	const LEFT = 'left';
	const NONE = 'none';
	const RIGHT = 'right';

	/** @return Df_Admin_Model_Config_Source_Layout_Float */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}