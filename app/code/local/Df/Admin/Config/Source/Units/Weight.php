<?php
/**
 * @singleton
 * Система создаёт объект-одиночку для потомков этого класса.
 * Не забывайте об этом при реализации кеширования результатов вычислений внутри этого класса!
 */
class Df_Admin_Config_Source_Units_Weight extends Df_Admin_Config_Source {
	/**
	 * @override
	 * @param bool $isMultiSelect
	 * @return array(array(string => string))
	 */
	protected function toOptionArrayInternal($isMultiSelect = false) {return $this->getAsOptionArray();}

	/** @return array(array(string => string)) */
	private function getAsOptionArray() {
		// Здесь кэшировать результат можно,
		// потому что у класса нет параметров.
		if (!isset($this->{__METHOD__})) {
			/** @var array(array(string => string)) $result */
			$result = [];
			foreach (df_weight()->getUnitsSettings() as $unitId => $unitData) {
				/** @var array(string => string|int) $unitData */
				$result[]= df_option($unitId, dfa($unitData, Df_Core_Model_Units_Weight::UNIT__LABEL));
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}
}