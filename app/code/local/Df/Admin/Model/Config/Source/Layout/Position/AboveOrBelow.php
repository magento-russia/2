<?php
/**
 * @singleton
 * Система создаёт объект-одиночку для потомков этого класса.
 * Не забывайте об этом при реализации кеширования результатов вычислений внутри этого класса!
 */
class Df_Admin_Model_Config_Source_Layout_Position_AboveOrBelow extends Df_Admin_Model_Config_Source {
	/**
	 * @override
	 * @param bool $isMultiSelect
	 * @return array(array(string => string))
	 */
	protected function toOptionArrayInternal($isMultiSelect = false) {
		return array(
			array(
				self::OPTION_KEY__LABEL => $this->getLabel('above')
				,self::OPTION_KEY__VALUE => self::ABOVE
			)
			,array(
				self::OPTION_KEY__LABEL => $this->getLabel('below')
				,self::OPTION_KEY__VALUE => self::BELOW
			)
		);
	}

	/**
	 * @param string $simpleLabel
	 * @return string
	 */
	private function getLabel($simpleLabel) {
		return df_h()->admin()->__(rm_concat_clean(' ', $simpleLabel, $this->getSuffix()));
	}

	/** @return string */
	private function getSuffix() {return $this->getFieldParam(self::CONFIG_PARAM__DF_LABEL_SUFFIX);}

	const _CLASS = __CLASS__;
	const ABOVE = 'above';
	const BELOW = 'below';
	const CONFIG_PARAM__DF_LABEL_SUFFIX = 'df_label_suffix';

	/** @return Df_Admin_Model_Config_Source_Layout_Position_AboveOrBelow */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}