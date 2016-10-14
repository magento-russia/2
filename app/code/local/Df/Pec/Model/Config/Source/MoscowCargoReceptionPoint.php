<?php
/**
 * @singleton
 * Система создаёт объект-одиночку для потомков этого класса.
 * Не забывайте об этом при реализации кеширования результатов вычислений внутри этого класса!
 */
class Df_Pec_Model_Config_Source_MoscowCargoReceptionPoint extends Df_Admin_Config_Source {
	/**
	 * @override
	 * @param bool $isMultiSelect
	 * @return array(array(string => string))
	 */
	protected function toOptionArrayInternal($isMultiSelect = false) {
		return rm_map_to_options(array(
			self::OPTION_VALUE__OUTSIDE => 'за пределами перечисленного в других пунктах'
			,self::OPTION_VALUE__INSIDE_LITTLE_RING_RAILWAY => 'внутри малого кольца Московской железной дороги'
			,self::OPTION_VALUE__INSIDE_THIRD_RING_ROAD => 'внутри Третьего транспортнонр кольца'
			,self::OPTION_VALUE__INSIDE_GARDEN_RING => 'внутри Садового кольца'
		));
	}
	const _C = __CLASS__;
	const OPTION_VALUE__INSIDE_GARDEN_RING = 'inside_garden_ring';
	const OPTION_VALUE__INSIDE_LITTLE_RING_RAILWAY = 'inside_little_ring_railway';
	const OPTION_VALUE__INSIDE_THIRD_RING_ROAD = 'inside_third_ring_road';
	const OPTION_VALUE__OUTSIDE = 'outside';
}