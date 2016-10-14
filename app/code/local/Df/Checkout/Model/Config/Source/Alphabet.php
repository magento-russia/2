<?php
/**
 * @singleton
 * Система создаёт объект-одиночку для потомков этого класса.
 * Не забывайте об этом при реализации кеширования результатов вычислений внутри этого класса!
 */
class Df_Checkout_Model_Config_Source_Alphabet extends Df_Admin_Config_Source {
	/**
	 * @override
	 * @param bool $isMultiSelect
	 * @return array(array(string => string))
	 */
	protected function toOptionArrayInternal($isMultiSelect = false) {
		return df_map_to_options(array(
			self::VALUE__RU => 'русский'
			,self::VALUE__UA => 'украинский и русский'
			,self::VALUE__KZ => 'казахский и русский'
		));
	}
	const _C = __CLASS__;
	const VALUE__KZ = 'kz';
	const VALUE__RU = 'ru';
	const VALUE__UA = 'ua';
}