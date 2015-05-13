<?php
/**
 * @singleton
 * Система создаёт объект-одиночку для потомков этого класса.
 * Не забывайте об этом при реализации кеширования результатов вычислений внутри этого класса!
 */
class Df_Checkout_Model_Config_Source_Alphabet extends Df_Admin_Model_Config_Source {
	/**
	 * @override
	 * @param bool $isMultiSelect
	 * @return array(array(string => string))
	 */
	protected function toOptionArrayInternal($isMultiSelect = false) {
		return
			array(
				array(
					self::OPTION_KEY__LABEL => 'русский'
					,self::OPTION_KEY__VALUE => self::VALUE__RU
				)
				,array(
					self::OPTION_KEY__LABEL => 'украинский и русский'
					,self::OPTION_KEY__VALUE => self::VALUE__UA
				)
				,array(
					self::OPTION_KEY__LABEL => 'казахский и русский'
					,self::OPTION_KEY__VALUE => self::VALUE__KZ
				)
			)
		;
	}
	const _CLASS = __CLASS__;
	const VALUE__KZ = 'kz';
	const VALUE__RU = 'ru';
	const VALUE__UA = 'ua';
}