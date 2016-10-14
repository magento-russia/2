<?php
/**
 * @singleton
 * Система создаёт объект-одиночку для потомков этого класса.
 * Не забывайте об этом при реализации кеширования результатов вычислений внутри этого класса!
 */
class Df_Admin_Config_Source_JqueryLoadMode extends Df_Admin_Config_Source {
	/**
	 * @override
	 * @param bool $isMultiSelect
	 * @return array(array(string => string))
	 */
	protected function toOptionArrayInternal($isMultiSelect = false) {
		return rm_map_to_options(array(
			self::$LOAD_FROM_GOOGLE => 'загружать с серверов Google'
			,'load-from-local' => 'загружать с сервера магазина'
			,self::$NO_LOAD => 'не загружать'
		));
	}
	/** @var string */
	private static $LOAD_FROM_GOOGLE = 'load-from-google';
	/** @var string */
	private static $NO_LOAD = 'no-load';

	/**
	 * @used-by Df_Core_Model_Settings_Jquery::fromGoogle()
	 * @param string $value
	 * @return bool
	 */
	public static function google($value) {return self::$LOAD_FROM_GOOGLE === $value;}

	/**
	 * @used-by Df_Core_Model_Settings_Jquery::needLoad()
	 * @param string $value
	 * @return bool
	 */
	public static function no($value) {return self::$NO_LOAD === $value;}
}