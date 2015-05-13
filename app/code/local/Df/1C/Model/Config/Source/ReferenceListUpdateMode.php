<?php
/**
 * @singleton
 * Система создаёт объект-одиночку для потомков этого класса.
 * Не забывайте об этом при реализации кеширования результатов вычислений внутри этого класса!
 */
class Df_1C_Model_Config_Source_ReferenceListUpdateMode extends Df_Admin_Model_Config_Source {
	/**
	 * @override
	 * @param bool $isMultiSelect
	 * @return array(array(string => string))
	 */
	protected function toOptionArrayInternal($isMultiSelect = false) {
		return
			array(
				array(
					self::OPTION_KEY__LABEL => 'сохранять все'
					,self::OPTION_KEY__VALUE => self::VALUE__ALL
				)
				,array(
					self::OPTION_KEY__LABEL => 'сохранять только добавленные вручную администратором'
					,self::OPTION_KEY__VALUE => self::VALUE__MANUAL_ONLY
				)
			,array(
				self::OPTION_KEY__LABEL => 'не сохранять'
				,self::OPTION_KEY__VALUE => self::VALUE__NONE
			)
			)
		;
	}
	const _CLASS = __CLASS__;
	const VALUE__ALL = 'all';
	const VALUE__MANUAL_ONLY = 'manual-only';
	const VALUE__NONE = 'none';
}


