<?php
/**
 * 2016-10-25
 * В случае отсутствия значения намеренно возвращаем 0, а не null,
 * чтобы можно было затем использовать @see array_flip():
 * для целых чисел эта операция работает, а для null — нет.
 * @param string $nameUc  
 * @return int
 */
function df_region_id_by_name_ru($nameUc) {
	/** @var array(string => int) $map */
	static $map;
	if (!$map) {
		/** @var \Df_Directory_Model_Resource_Region_Collection $regions */
		$regions = df_h()->directory()->getRussianRegions();
		$map = array_combine(df_strtoupper($regions->walk('getName')), $regions->walk('getId'));
	}
	return dfa($map, $nameUc, 0);
}


