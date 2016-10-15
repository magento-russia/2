<?php
class Df_SalesRule_Model_Rule_Condition_Product_Subselect extends Mage_SalesRule_Model_Rule_Condition_Product_Subselect {
	/**
	 * Цель перекрытия —
	 * заплатка для опции «Группа товаров с определёнными условиями».
	 * @override
	 * @return Df_SalesRule_Model_Rule_Condition_Product_Subselect
	 */
	public function loadValueOptions() {
		parent::loadValueOptions();
		/** @var bool $needFix */
		static $needFix; if (is_null($needFix)) {$needFix =
			df_cfg()->admin()->promotions()->getFixProductsSubselection()
		;}
		if ($needFix) {
			$valueOptions = $this[self::DF_FIELD_VALUE_OPTION];
			if (!is_array($valueOptions) && !($valueOptions instanceof Traversable)) {
				$this[self::DF_FIELD_VALUE_OPTION] = array(1 => 'да', 0 => 'нет');
			}
		}
		return $this;
	}

	const DF_FIELD_VALUE_OPTION = 'value_option';
}