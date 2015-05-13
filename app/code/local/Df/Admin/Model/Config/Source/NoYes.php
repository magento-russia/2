<?php
/**
 * @singleton
 * Система создаёт объект-одиночку для потомков этого класса.
 * Не забывайте об этом при реализации кеширования результатов вычислений внутри этого класса!
 */
class Df_Admin_Model_Config_Source_NoYes extends Df_Admin_Model_Config_Source {
	/**
	 * @override
	 * @param bool $isMultiSelect
	 * @return array(array(string => string))
	 */
	protected function toOptionArrayInternal($isMultiSelect = false) {
		return
			array(
				array(
					'value' => 0
					,'label' => df_mage()->adminhtmlHelper()->__('Yes')
				)
				,array(
					'value' => 1
					,'label' => df_mage()->adminhtmlHelper()->__('No')
				)
			)
		;
	}

	/**
	 * @override
	 * @param array $arrAttributes
	 * @return array(int => string)
	 */
	public function toArray(array $arrAttributes = array()) {
		return $this->toOptionArrayAssoc();
	}
	const _CLASS = __CLASS__;

	/** @return Df_Admin_Model_Config_Source_NoYes */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}