<?php
/**
 * @singleton
 * Система создаёт объект-одиночку для потомков этого класса.
 * Не забывайте об этом при реализации кеширования результатов вычислений внутри этого класса!
 */
class Df_Cms_Model_Config_Source_ContentsMenu_Position extends Df_Admin_Config_Source {
	/**
	 * @override
	 * @param bool $isMultiSelect
	 * @return array(array(string => string))
	 */
	protected function toOptionArrayInternal($isMultiSelect = false) {
		return rm_map_to_options(array(
			self::CONTENT => 'Content', self::LEFT => 'Left Column', self::RIGHT => 'Right Column'
		), $this);
	}
	const _C = __CLASS__;
	const CONTENT = 'content';
	const LEFT = 'left';
	const RIGHT = 'right';

	/** @return Df_Cms_Model_Config_Source_ContentsMenu_Position */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}