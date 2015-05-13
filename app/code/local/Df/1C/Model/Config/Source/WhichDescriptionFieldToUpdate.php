<?php
/**
 * @singleton
 * Система создаёт объект-одиночку для потомков этого класса.
 * Не забывайте об этом при реализации кеширования результатов вычислений внутри этого класса!
 */
class Df_1C_Model_Config_Source_WhichDescriptionFieldToUpdate extends Df_Admin_Model_Config_Source {
	/**
	 * @override
	 * @param bool $isMultiSelect
	 * @return array(array(string => string))
	 */
	protected function toOptionArrayInternal($isMultiSelect = false) {
		return
			array(
				array(
					self::OPTION_KEY__LABEL => 'описание'
					,self::OPTION_KEY__VALUE => self::VALUE__DESCRIPTION
				)
				,array(
					self::OPTION_KEY__LABEL => 'краткое описание'
					,self::OPTION_KEY__VALUE => self::VALUE__SHORT_DESCRIPTION
				)
				,array(
					self::OPTION_KEY__LABEL => 'описание и краткое описание'
					,self::OPTION_KEY__VALUE => self::VALUE__BOTH
				)
				,array(
					self::OPTION_KEY__LABEL => 'никакое'
					,self::OPTION_KEY__VALUE => self::VALUE__NONE
				)
			)
		;
	}
	const _CLASS = __CLASS__;
	const VALUE__BOTH = 'both';
	const VALUE__DESCRIPTION = 'description';
	const VALUE__SHORT_DESCRIPTION = 'short_description';
	const VALUE__NONE = 'none';
}


