<?php
/**
 * @singleton
 * Система создаёт объект-одиночку для потомков этого класса.
 * Не забывайте об этом при реализации кеширования результатов вычислений внутри этого класса!
 */
class Df_Checkout_Model_Config_Source_Field_Applicability extends Df_Admin_Config_Source {
	/**
	 * @override
	 * @param bool $isMultiSelect
	 * @return array(array(string => string))
	 */
	protected function toOptionArrayInternal($isMultiSelect = false) {
		return df_map_to_options(array(
			self::VALUE__NO => 'убрать'
			,self::VALUE__OPTIONAL => 'заполнять по желанию'
			,self::VALUE__REQUIRED => 'заполнять обязательно'
		));
	}
	const _C = __CLASS__;
	const VALUE__NO = 'no';
	const VALUE__OPTIONAL = 'optional';
	const VALUE__REQUIRED = 'required';
}