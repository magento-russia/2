<?php
/**
 * @singleton
 * Система создаёт объект-одиночку для потомков этого класса.
 * Не забывайте об этом при реализации кеширования результатов вычислений внутри этого класса!
 */
class Df_Admin_Config_Source_HideFromAnonymous extends Df_Admin_Config_Source {
	/**
	 * @override
	 * @param bool $isMultiSelect
	 * @return array(array(string => string))
	 */
	protected function toOptionArrayInternal($isMultiSelect = false) {
		return df_map_to_options(array(
			self::$V__NO_HIDE => 'не скрывать'
			,self::$V__HIDE => 'скрыть'
			,self::$V__HIDE_FROM_ANONYMOUS => 'скрыть от анонимных посетителей'
		));
	}
	/** @var int */
	private static $V__HIDE = 1;
	/** @var string */
	private static $V__HIDE_FROM_ANONYMOUS = 'hide-from-anonymous';
	/** @var int */
	private static $V__NO_HIDE = 0;

	/**
	 * @used-by Df_Tweaks_Model_Handler_Header_AdjustLinks::handle()
	 * @param mixed $value
	 * @return bool
	 */
	public static function needHide($value) {
		return
			self::$V__HIDE === (int)$value
			|| !df_customer_logged_in() && self::$V__HIDE_FROM_ANONYMOUS === $value
		;
	}
}