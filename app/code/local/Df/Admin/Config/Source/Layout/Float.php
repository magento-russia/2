<?php
/**
 * @singleton
 * Система создаёт объект-одиночку для потомков этого класса.
 * Не забывайте об этом при реализации кеширования результатов вычислений внутри этого класса!
 */
class Df_Admin_Config_Source_Layout_Float extends Df_Admin_Config_Source {
	/**
	 * @override
	 * @param bool $isMultiSelect
	 * @return array(array(string => string))
	 */
	protected function toOptionArrayInternal($isMultiSelect = false) {
		return rm_map_to_options(array(
			self::$NONE => 'нет'
			,'left' => 'прислонить к левому краю родительского блока (обтекание справо)'
			,'right' => 'прислонить к правому краю родительского блока (обтекание слева)'
		));
	}

	/**
	 * @param string $value
	 * @return bool
	 */
	public static function isNone($value) {return self::$NONE === $value;}

	/** @var string */
	private static $NONE = 'none';
}