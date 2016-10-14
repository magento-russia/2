<?php
/**
 * @singleton
 * Система создаёт объект-одиночку для потомков этого класса.
 * Не забывайте об этом при реализации кеширования результатов вычислений внутри этого класса!
 */
class Df_Admin_Config_Source_Layout_Column extends Df_Admin_Config_Source {
	/**
	 * @override
	 * @param bool $isMultiSelect
	 * @return array(array(string => string))
	 */
	protected function toOptionArrayInternal($isMultiSelect = false) {
		return array_merge(
			$this->needShowOptionNo() ? array(rm_option('no', 'не показывать')) : array()
			, df_map_to_options(array(self::$LEFT => 'левая колонка', self::$RIGHT => 'правая колонка'))
		);
	}

	/** @return bool */
	private function needShowOptionNo() {return rm_bool($this->getFieldParam('df_option_no', false));}

	/**
	 * @param string $value
	 * @return bool
	 */
	public static function isLeft($value) {return self::$LEFT === $value;}

	/**
	 * @param string $value
	 * @return bool
	 */
	public static function isRight($value) {return self::$RIGHT === $value;}

	/** @var string */
	private static $LEFT = 'left';
	/** @var string */
	private static $RIGHT = 'right';
}