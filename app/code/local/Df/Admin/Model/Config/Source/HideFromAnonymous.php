<?php
/**
 * @singleton
 * Система создаёт объект-одиночку для потомков этого класса.
 * Не забывайте об этом при реализации кеширования результатов вычислений внутри этого класса!
 */
class Df_Admin_Model_Config_Source_HideFromAnonymous extends Df_Admin_Model_Config_Source {
	/**
	 * @override
	 * @param bool $isMultiSelect
	 * @return array(array(string => string))
	 */
	protected function toOptionArrayInternal($isMultiSelect = false) {
		return
			array(
				array(
					self::OPTION_KEY__LABEL => 'не скрывать'
					,self::OPTION_KEY__VALUE => self::VALUE__NO_HIDE
				)
				,array(
					self::OPTION_KEY__LABEL => 'скрыть'
					,self::OPTION_KEY__VALUE => self::VALUE__HIDE
				)
				,array(
					self::OPTION_KEY__LABEL => 'скрыть от анонимных посетителей'
					,self::OPTION_KEY__VALUE => self::VALUE__HIDE_FROM_ANONYMOUS
				)
			)
		;
	}
	const _CLASS = __CLASS__;
	const VALUE__HIDE = 1;
	const VALUE__HIDE_FROM_ANONYMOUS = 'hide-from-anonymous';
	const VALUE__NO_HIDE = 0;

	/** @return Df_Admin_Model_Config_Source_HideFromAnonymous */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}