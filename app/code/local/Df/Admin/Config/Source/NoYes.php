<?php
/**
 * @singleton
 * Система создаёт объект-одиночку для потомков этого класса.
 * Не забывайте об этом при реализации кеширования результатов вычислений внутри этого класса!
 */
class Df_Admin_Config_Source_NoYes extends Df_Admin_Config_Source {
	/**
	 * @override
	 * @param bool $isMultiSelect
	 * @return array(array(string => string))
	 */
	protected function toOptionArrayInternal($isMultiSelect = false) {
		return rm_map_to_options(array('Yes', 'No'), 'Mage_Adminhtml');
	}

	/**
	 * @override
	 * @param array $arrAttributes
	 * @return array(int => string)
	 */
	public function toArray(array $arrAttributes = array()) {return $this->toOptionArrayAssoc();}
}