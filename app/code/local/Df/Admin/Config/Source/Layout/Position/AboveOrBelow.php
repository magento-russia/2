<?php
/**
 * @singleton
 * Система создаёт объект-одиночку для потомков этого класса.
 * Не забывайте об этом при реализации кеширования результатов вычислений внутри этого класса!
 */
class Df_Admin_Config_Source_Layout_Position_AboveOrBelow extends Df_Admin_Config_Source {
	/**
	 * 2015-03-07
	 * Константы нам не нужны, потому что эти значения используются только в JavaScript:
	 * @see df/checkout/review/orderComments.phtml
	 * @override
	 * @param bool $isMultiSelect
	 * @return array(array(string => string))
	 */
	protected function toOptionArrayInternal($isMultiSelect = false) {
		return df_map_to_options(array('above' => $this->l('above'), 'below' => $this->l('below')));
	}

	/**
	 * @param string $simpleLabel
	 * @return string
	 */
	private function l($simpleLabel) {
		return df_h()->admin()->__(df_ccc(' ', $simpleLabel, $this->getSuffix()));
	}

	/** @return string */
	private function getSuffix() {return $this->getFieldParam('df_label_suffix');}
}