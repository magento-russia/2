<?php
/**
 * @singleton
 * Система создаёт объект-одиночку для потомков этого класса.
 * Не забывайте об этом при реализации кеширования результатов вычислений внутри этого класса!
 */
class Df_Checkout_Model_Config_Source_Field_Applicability extends Df_Admin_Model_Config_Source {
	/**
	 * @override
	 * @param bool $isMultiSelect
	 * @return array(array(string => string))
	 */
	protected function toOptionArrayInternal($isMultiSelect = false) {
		return
			array(
				array(
					self::OPTION_KEY__LABEL => 'убрать'
					,self::OPTION_KEY__VALUE => self::VALUE__NO
				)
				,array(
					self::OPTION_KEY__LABEL => 'заполнять по желанию'
					,self::OPTION_KEY__VALUE => self::VALUE__OPTIONAL
				)
				,array(
					self::OPTION_KEY__LABEL => 'заполнять обязательно'
					,self::OPTION_KEY__VALUE => self::VALUE__REQUIRED
				)
			)
		;
	}
	const _CLASS = __CLASS__;
	const VALUE__NO = 'no';
	const VALUE__OPTIONAL = 'optional';
	const VALUE__REQUIRED = 'required';
}