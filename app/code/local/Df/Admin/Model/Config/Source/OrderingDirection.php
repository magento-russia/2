<?php
/**
 * @singleton
 * Система создаёт объект-одиночку для потомков этого класса.
 * Не забывайте об этом при реализации кеширования результатов вычислений внутри этого класса!
 */
class Df_Admin_Model_Config_Source_OrderingDirection extends Df_Admin_Model_Config_Source {
	/**
	 * @override
	 * @param bool $isMultiSelect
	 * @return array(array(string => string))
	 */
	protected function toOptionArrayInternal($isMultiSelect = false) {
		return
			array(
				array(
					self::OPTION_KEY__LABEL => 'по возрастанию'
					,self::OPTION_KEY__VALUE => Varien_Data_Collection::SORT_ORDER_ASC
				)
				,array(
					self::OPTION_KEY__LABEL => 'по убыванию'
					,self::OPTION_KEY__VALUE => Varien_Data_Collection::SORT_ORDER_DESC
				)
			)
		;
	}
	const _CLASS = __CLASS__;

	/** @return Df_Admin_Model_Config_Source_OrderingDirection */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}