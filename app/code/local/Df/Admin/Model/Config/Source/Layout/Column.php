<?php
/**
 * @singleton
 * Система создаёт объект-одиночку для потомков этого класса.
 * Не забывайте об этом при реализации кеширования результатов вычислений внутри этого класса!
 */
class Df_Admin_Model_Config_Source_Layout_Column extends Df_Admin_Model_Config_Source {
	/**
	 * @override
	 * @param bool $isMultiSelect
	 * @return array(array(string => string))
	 */
	protected function toOptionArrayInternal($isMultiSelect = false) {
		/** @var string[][] $result */
		$result = array();
		if ($this->needShowOptionNo()) {
			$result[]=
				array(
					self::OPTION_KEY__LABEL => 'не показывать'
					,self::OPTION_KEY__VALUE => self::OPTION_VALUE__NO
				)
			;
		}
		$result =
			array_merge(
				$result
				,array(
					array(
						self::OPTION_KEY__LABEL => 'левая колонка'
						,self::OPTION_KEY__VALUE => self::OPTION_VALUE__LEFT
					)
					,array(
						self::OPTION_KEY__LABEL => 'правая колонка'
						,self::OPTION_KEY__VALUE => self::OPTION_VALUE__RIGHT
					)
				)
			)
		;
		return $result;
	}

	/** @return bool */
	private function needShowOptionNo() {
		return rm_bool($this->getFieldParam(self::CONFIG_PARAM__DF_OPTION_NO, false));
	}
	const _CLASS = __CLASS__;
	const CONFIG_PARAM__DF_OPTION_NO = 'df_option_no';
	const OPTION_VALUE__LEFT = 'left';
	const OPTION_VALUE__NO = 'no';
	const OPTION_VALUE__RIGHT = 'right';

	/** @return Df_Admin_Model_Config_Source_Layout_Column */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}