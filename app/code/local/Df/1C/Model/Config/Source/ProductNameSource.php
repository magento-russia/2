<?php
/**
 * @singleton
 * Система создаёт объект-одиночку для потомков этого класса.
 * Не забывайте об этом при реализации кеширования результатов вычислений внутри этого класса!
 */
class Df_1C_Model_Config_Source_ProductNameSource extends Df_Admin_Model_Config_Source {
	/**
	 * @override
	 * @param bool $isMultiSelect
	 * @return array(array(string => string))
	 */
	protected function toOptionArrayInternal($isMultiSelect = false) {
		return
			array(
				array(
					self::OPTION_KEY__LABEL => '«Наименование» («Рабочее наименование»)'
					,self::OPTION_KEY__VALUE => self::VALUE__NAME
				)
				,array(
					self::OPTION_KEY__LABEL => '«Полное наименование» («Наименование для печати»)'
					,self::OPTION_KEY__VALUE => self::VALUE__NAME_FULL
				)
			)
		;
	}
	const _CLASS = __CLASS__;
	const VALUE__NAME = 'name';
	const VALUE__NAME_FULL = 'name_full';
}


