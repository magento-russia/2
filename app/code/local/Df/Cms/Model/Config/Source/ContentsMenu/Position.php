<?php
/**
 * @singleton
 * Система создаёт объект-одиночку для потомков этого класса.
 * Не забывайте об этом при реализации кеширования результатов вычислений внутри этого класса!
 */
class Df_Cms_Model_Config_Source_ContentsMenu_Position extends Df_Admin_Model_Config_Source {
	/**
	 * @override
	 * @param bool $isMultiSelect
	 * @return array(array(string => string))
	 */
	protected function toOptionArrayInternal($isMultiSelect = false) {
		return
			array(
				array(
					self::OPTION_KEY__LABEL => df_h()->cms()->__('Content')
					,self::OPTION_KEY__VALUE => self::CONTENT
				)
				,array(
					self::OPTION_KEY__LABEL => df_h()->cms()->__('Left Column')
					,self::OPTION_KEY__VALUE => self::LEFT
				)
				,array(
					self::OPTION_KEY__LABEL => df_h()->cms()->__('Right Column')
					,self::OPTION_KEY__VALUE => self::RIGHT
				)
			)
		;
	}
	const _CLASS = __CLASS__;
	const CONTENT = 'content';
	const LEFT = 'left';
	const RIGHT = 'right';

	/** @return Df_Cms_Model_Config_Source_ContentsMenu_Position */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}