<?php
/**
 * @singleton
 * Система создаёт объект-одиночку для потомков этого класса.
 * Не забывайте об этом при реализации кеширования результатов вычислений внутри этого класса!
 */
class Df_Admin_Model_Config_Source_YesNoDev extends Df_Admin_Model_Config_Source {
	/**
	 * @override
	 * @param bool $isMultiSelect
	 * @return array(array(string => string))
	 */
	protected function toOptionArrayInternal($isMultiSelect = false) {
		return
			array(
				array(
					self::OPTION_KEY__LABEL => 'да'
					,self::OPTION_KEY__VALUE => self::VALUE__YES
				)
				,array(
					self::OPTION_KEY__LABEL => 'нет'
					,self::OPTION_KEY__VALUE => self::VALUE__NO
				)
				,array(
					self::OPTION_KEY__LABEL =>
						rm_sprintf(
							'только при %s режиме разработчика'
							,$this->needEnableInDeveloperMode()
							? 'включенном'
							: 'отключенном'
						)
					,self::OPTION_KEY__VALUE => self::VALUE__DEVELOPER_MODE
				)
			)
		;
	}

	/** @return bool */
	private function needEnableInDeveloperMode() {
		return rm_bool($this->getFieldParam(self::CONFIG_PARAM__DF_ENABLE_IN_DEVELOPER_MODE, 1));
	}
	const _CLASS = __CLASS__;
	const CONFIG_PARAM__DF_ENABLE_IN_DEVELOPER_MODE = 'df_enable_in_developer_mode';
	const VALUE__DEVELOPER_MODE = 'developer-mode';
	const VALUE__NO = 'no';
	const VALUE__YES = 'yes';

	/** @return Df_Admin_Model_Config_Source_YesNoDev */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}