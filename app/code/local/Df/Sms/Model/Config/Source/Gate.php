<?php
/**
 * @singleton
 * Система создаёт объект-одиночку для потомков этого класса.
 * Не забывайте об этом при реализации кеширования результатов вычислений внутри этого класса!
 */
class Df_Sms_Model_Config_Source_Gate extends Df_Admin_Model_Config_Source {
	/**
	 * @override
	 * @param bool $isMultiSelect
	 * @return array(array(string => string))
	 */
	protected function toOptionArrayInternal($isMultiSelect = false) {
		return
			array(
				array(
					self::OPTION_KEY__LABEL => '-- выберите шлюз --'
					,self::OPTION_KEY__VALUE => ''
				)
				,array(
					self::OPTION_KEY__LABEL => 'sms16.ru'
					,self::OPTION_KEY__VALUE => Df_Sms_Model_Gate_Sms16Ru::RM__ID
				)
			)
		;
	}
	const _CLASS = __CLASS__;
	const VALUE__NAME = 'name';
	const VALUE__NAME_FULL = 'name_full';
}


