<?php
/**
 * @singleton
 * Система создаёт объект-одиночку для потомков этого класса.
 * Не забывайте об этом при реализации кеширования результатов вычислений внутри этого класса!
 */
class Df_Admin_Config_Source_SelectNumberFromDropdown extends Df_Admin_Config_Source {
	/**
	 * @override
	 * @param bool $isMultiSelect
	 * @return array(array(int => int))
	 */
	protected function toOptionArrayInternal($isMultiSelect = false) {return $this->getAsOptionArray();}

	/** @return array(array(int => int)) */
	private function getAsOptionArray() {
		// Кэшировать результат нельзя,
		// потому что система использует единый объект для всех элементов управления данного типа!
		/** @var int[][] $result */
		$result = array();
		/** @var int $max */
		$max = df_nat0($this->getFieldParam(self::$DF_MAX, 10));
		/** @var int $step */
		$step = df_nat0($this->getFieldParam('df_step', 1));;
		for ($i = $this->getMin(); $i <= $max; $i += $step) {
			$result[]= rm_option($i, $i);
		}
		return $result;
	}

	/** @return int */
	private function getMin() {return df_nat0($this->getFieldParam('df_min', 1));}

	/**
	 * @used-by getAsOptionArray()
	 * @used-by i()
	 * @var string
	 */
	private static $DF_MAX = 'df_max';
	/**
	 * @used-by Df_Cms_Model_Admin_Config_Node_ContentsMenu_VisibilitySettings_PageType::getOptionsVerticalOrdering()
	 * @static
	 * @param int $max
	 * @return Df_Admin_Config_Source_SelectNumberFromDropdown
	 */
	public static function i($max) {return new self(array(self::$DF_MAX => $max));}
}