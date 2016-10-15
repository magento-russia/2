<?php
class Df_Tax_M extends Df_Core_Model {
	/**
	 * 2015-08-09
	 * Находит в БД или создаёт при отсутствии заданную налоговую ставку.
	 * @used-by rm_product_tax_class_id()
	 * @param float $rate
	 * @return int|null
	 */
	public function productClassId($rate) {
		/** @var array(string => array(string => int)) $cache */
		static $cache;
		/** @var string $iso2 */
		$iso2 = rm_shop_iso2(rm_state()->getStoreProcessed());
		/** @var string $rateS */
		$rateS = (string)$rate;
		if (!isset($cache[$iso2][$rateS])) {
			/** @var int|null $result */
			$result = null;
			/** @var Zend_Db_Select $select */
			$select = df_select()
				->from(array('calc' => df_table('tax_calculation')), 'product_tax_class_id')
				->joinInner(
					array('rate' => df_table('tax_calculation_rate'))
					,'calc.tax_calculation_rate_id = rate.tax_calculation_rate_id'
					,null
				)
				->where("rate.tax_postcode = '*'")
				->where("rate.tax_region_id = 0")
				->where('rate.zip_is_range IS NULL')
				->where('rate.zip_from IS NULL')
				->where('rate.zip_to IS NULL')
				->where('(? = rate.tax_country_id)', $iso2)
				->where('(? = rate.rate)', $rate)
			;
			/** @var Zend_Db_Statement_Pdo $query */
			$query = df_conn()->query($select);
			/** @var array(array(string => string)) $rows */
			$rows = $query->fetchAll($style = Zend_Db::FETCH_ASSOC);
			/** @var int $count */
			$count = count($rows);
			if (1 !== $count) {
				/** @var string $countryName */
				$countryName = rm_country_ctn_ru($iso2);
				/**
				 * 2015-08-09
				 * PHP сам отбросит нули в конце ставки:
				 * http://3v4l.org/T2Gul
				 * http://stackoverflow.com/a/5149186
				 */
				df_error(
					"В интернет-магазине надо правильно настроить НДС {$rate}%"
					. " для страны «{$countryName}».\nСейчас "
					.
						!$count
						? "требуемая ставка НДС в интернет-магазине не найдена."
						: "найдено сразу несколько ({$count}) ставок НДС,"
							. " удовлетворяющих заданным условиям,"
							. " поэтому система запуталась."
							. "\nУсловиям должна удовлетворять только одна ставка."
				);
			}
			/** @var int|null $result */
			$result = dfa($rows[0], 'product_tax_class_id');
			if (!is_null($result)) {
				$result = df_nat($result);
			}
			$cache[$iso2][$rateS] = df_n_set($result);
		}
		return df_n_get($cache[$iso2][$rateS]);
	}

	/**
	 * @override
	 * @see Df_Core_Model::cached()
	 * @return string[]
	 */
	protected function cached() {return self::m(__CLASS__, 'productClassId');}

	/** @return Df_Tax_M */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}