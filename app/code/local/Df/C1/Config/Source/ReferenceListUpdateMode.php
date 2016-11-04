<?php
namespace Df\C1\Config\Source;
/**
 * @singleton
 * Система создаёт объект-одиночку для потомков этого класса.
 * Не забывайте об этом при реализации кеширования результатов вычислений внутри этого класса!
 */
class ReferenceListUpdateMode extends \Df_Admin_Config_Source {
	/**
	 * @override
	 * @param bool $isMultiSelect
	 * @return array(array(string => string))
	 */
	protected function toOptionArrayInternal($isMultiSelect = false) {
		return df_map_to_options(array(
			self::$VALUE__ALL => 'сохранять все'
			,'manual-only' => 'сохранять только добавленные вручную администратором'
			,self::$VALUE__NONE => 'не сохранять'
		));
	}
	/** @var string */
	private static $VALUE__ALL = 'all';
	/** @var string */
	private static $VALUE__NONE = 'none';

	/**
	 * @used-by Df_Eav_Model_Entity_Attribute_Option_Calculator::calculate()
	 * @param string $value
	 * @return bool
	 */
	public static function isAll($value) {return self::$VALUE__ALL === $value;}

	/**
	 * @used-by Df_Eav_Model_Entity_Attribute_Option_Calculator::calculate()
	 * @param string $value
	 * @return bool
	 */
	public static function isNone($value) {return self::$VALUE__NONE === $value;}
}