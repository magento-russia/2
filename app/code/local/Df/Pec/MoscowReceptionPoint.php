<?php
namespace Df\Pec;
/**
 * @singleton
 * Система создаёт объект-одиночку для потомков этого класса.
 * Не забывайте об этом при реализации кеширования результатов вычислений внутри этого класса!
 */
class MoscowReceptionPoint extends \Df_Admin_Config_Source {
	/**
	 * 2016-10-30
	 * http://pecom.ru/business/developers/api_public/
	 * @override
	 * @param bool $isMultiSelect [optional]
	 * @return array(array(string => string))
	 */
	protected function toOptionArrayInternal($isMultiSelect = false) {return df_map_to_options([
		'за пределами перечисленного в других пунктах'
		,'внутри малого кольца Московской железной дороги'
		,'внутри Третьего транспортного кольца'
		,'внутри Садового кольца'
	]);}
}