<?php
/**
 * @singleton
 * Система создаёт объект-одиночку для потомков этого класса.
 * Не забывайте об этом при реализации кеширования результатов вычислений внутри этого класса!
 */
class Df_Admin_Model_Config_Source_SelectNumberFromDropdown extends Df_Admin_Model_Config_Source {
	/**
	 * @override
	 * @param bool $isMultiSelect
	 * @return int[][]
	 */
	protected function toOptionArrayInternal($isMultiSelect = false) {return $this->getAsOptionArray();}

	/** @return int[][] */
	private function getAsOptionArray() {
		// Кэшировать результат нельзя,
		// потому что система использует единый объект для всех элементов управления данного типа!
		/** @var int[][] $result */
		$result = array();
		for($i = $this->getMin(); $i <= $this->getMax(); $i += $this->getStep()) {
			$result[]= array(self::OPTION_KEY__LABEL => $i, self::OPTION_KEY__VALUE => $i);
		}
		return $result;
	}

	/** @return int */
	private function getMax() {return rm_nat0($this->getFieldParam(self::CONFIG_PARAM__DF_MAX, 10));}

	/** @return int */
	private function getMin() {return rm_nat0($this->getFieldParam(self::CONFIG_PARAM__DF_MIN, 1));}

	/** @return int */
	private function getStep() {return rm_nat0($this->getFieldParam(self::CONFIG_PARAM__DF_STEP, 1));}

	const _CLASS = __CLASS__;
	const CONFIG_PARAM__DF_FORMAT = 'df_format';
	const CONFIG_PARAM__DF_MIN = 'df_min';
	const CONFIG_PARAM__DF_MAX = 'df_max';
	const CONFIG_PARAM__DF_STEP = 'df_step';
	/**
	 * @static
	 * @param int $max
	 * @return Df_Admin_Model_Config_Source_SelectNumberFromDropdown
	 */
	public static function i($max) {return new self(array(self::CONFIG_PARAM__DF_MAX => $max));}
}