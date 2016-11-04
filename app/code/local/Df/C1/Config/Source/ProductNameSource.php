<?php
namespace Df\C1\Config\Source;
/**
 * @singleton
 * Система создаёт объект-одиночку для потомков этого класса.
 * Не забывайте об этом при реализации кеширования результатов вычислений внутри этого класса!
 */
class ProductNameSource extends \Df_Admin_Config_Source {
	/**
	 * @override
	 * @param bool $isMultiSelect
	 * @return array(array(string => string))
	 */
	protected function toOptionArrayInternal($isMultiSelect = false) {return df_map_to_options([
		'name' => '«Наименование» («Рабочее наименование»)'
		,self::$VALUE__NAME_FULL => '«Полное наименование» («Наименование для печати»)'
	]);}

	/** @var string */
	private static $VALUE__NAME_FULL = 'name_full';

	/**
	 * @used-by \Df\C1\Cml2\Import\Processor\Product\Type::getName()
	 * @param string $name
	 * @return bool
	 */
	public static function isFull($name) {return self::$VALUE__NAME_FULL === $name;}
}


