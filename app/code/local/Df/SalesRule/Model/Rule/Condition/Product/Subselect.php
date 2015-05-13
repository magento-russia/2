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
		static $needFix;
		if (!isset($needFix)) {
			$needFix =
					df_enabled(Df_Core_Feature::TWEAKS_ADMIN)
				&&
					(df_cfg()->admin()->promotions()->getFixProductsSubselection())
			;
		}
		if ($needFix) {
			$valueOptions = $this[self::DF_FIELD_VALUE_OPTION];
			if (!is_array($valueOptions) && !($valueOptions instanceof Traversable)) {
				$this[self::DF_FIELD_VALUE_OPTION] = array(1 => 'да', 0 => 'нет');
			}
		}
		return $this;
	}
	const _CLASS = __CLASS__;
	const DF_FIELD_VALUE_OPTION = 'value_option';
}