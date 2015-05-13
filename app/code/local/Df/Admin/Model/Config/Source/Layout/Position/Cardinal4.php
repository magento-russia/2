<?php
/**
 * @singleton
 * Система создаёт объект-одиночку для потомков этого класса.
 * Не забывайте об этом при реализации кеширования результатов вычислений внутри этого класса!
 */
class Df_Admin_Model_Config_Source_Layout_Position_Cardinal4 extends Df_Admin_Model_Config_Source {
	/**
	 * @override
	 * @param bool $isMultiSelect
	 * @return array(array(string => string))
	 */
	protected function toOptionArrayInternal($isMultiSelect = false) {
		return
			array(
				array(
					self::OPTION_KEY__LABEL => 'слева'
					,self::OPTION_KEY__VALUE => self::LEFT
				)
				,array(
					self::OPTION_KEY__LABEL => 'справа'
					,self::OPTION_KEY__VALUE => self::RIGHT
				)
				,array(
					self::OPTION_KEY__LABEL => 'сверху'
					,self::OPTION_KEY__VALUE => self::TOP
				)
				,array(
					self::OPTION_KEY__LABEL => 'снизу'
					,self::OPTION_KEY__VALUE => self::BOTTOM
				)
			)
		;
	}
	const _CLASS = __CLASS__;
	const BOTTOM = 'bottom';
	const LEFT = 'left';
	const RIGHT = 'right';
	const TOP = 'top';

	/** @return Df_Admin_Model_Config_Source_Layout_Position_Cardinal4 */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}