<?php
use Df_Directory_Model_Region as Region;
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

/**
 * 2016-10-25
 * @param int|Region $r
 * @return Region
 */
function df_region($r) {return is_object($r) ? $r : Region::ld($r);}

/**
 * 2016-10-25
 * @param int|Region $r
 * @return string
 */
function df_region_name($r) {return df_region($r)->getName();}

/**
 * 2016-10-25
 * @param int|Region $r
 * @return string
 */
function df_region_name_uc($r) {return df_strtoupper(df_region_name($r));}


