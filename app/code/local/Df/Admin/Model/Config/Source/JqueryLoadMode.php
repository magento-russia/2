<?php
/**
 * @singleton
 * Система создаёт объект-одиночку для потомков этого класса.
 * Не забывайте об этом при реализации кеширования результатов вычислений внутри этого класса!
 */
class Df_Admin_Model_Config_Source_JqueryLoadMode extends Df_Admin_Model_Config_Source {
	/**
	 * @override
	 * @param bool $isMultiSelect
	 * @return array(array(string => string))
	 */
	protected function toOptionArrayInternal($isMultiSelect = false) {
		return
			array(
				array(
					self::OPTION_KEY__LABEL => 'загружать с серверов Google'
					,self::OPTION_KEY__VALUE => self::VALUE__LOAD_FROM_GOOGLE
				)
				,array(
					self::OPTION_KEY__LABEL => 'загружать с сервера магазина'
					,self::OPTION_KEY__VALUE => self::VALUE__LOAD_FROM_LOCAL
				)
				,array(
					self::OPTION_KEY__LABEL => 'не загружать'
					,self::OPTION_KEY__VALUE => self::VALUE__NO_LOAD
				)
			)
		;
	}
	const _CLASS = __CLASS__;
	const VALUE__LOAD_FROM_GOOGLE = 'load-from-google';
	const VALUE__LOAD_FROM_LOCAL = 'load-from-local';
	const VALUE__NO_LOAD = 'no-load';

	/** @return Df_Admin_Model_Config_Source_JqueryLoadMode */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}