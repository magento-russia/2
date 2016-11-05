<?php
class Df_SalesRule_Model_Rule extends Mage_SalesRule_Model_Rule {
	/**
	 * @override
	 * @return Df_SalesRule_Model_Resource_Rule_Collection
	 */
	public function getResourceCollection() {return self::c();}

	/**
	 * @override
	 * @return Df_SalesRule_Model_Resource_Rule
	 * 2016-10-14
	 * В родительском классе метод переобъявлен через PHPDoc,
	 * и поэтому среда разработки думает, что он публичен.
	 */
	/** @noinspection PhpHierarchyChecksInspection */
	protected function _getResource() {return Df_SalesRule_Model_Resource_Rule::s();}

	/**
	 * @used-by Df_PromoGift_Model_Gift::getRule()
	 * @used-by Df_SalesRule_Model_Resource_Rule_Collection::_construct()
	 */

	const ENTITY = 'salesrule_rule';
	const P__BASE_DISCOUNT_AMOUNT = 'base_discount_amount';
	const P__IS_ACTIVE = 'is_active';
	const P__DISCOUNT_AMOUNT = 'discount_amount';
	const P__FROM_DATE = 'from_date';
	const P__TO_DATE = 'to_date';
	const P__USES_PER_CUSTOMER = 'uses_per_customer';
	const P__WEBSITE_IDS = 'website_ids';
	/**
	 * В Magento 1.5.0.1 данная константа определена в классе Mage_SalesRule_Model_Rule:
	 * @see Mage_SalesRule_Model_Rule::BY_PERCENT_ACTION
	 * В Magento 1.4.0.1 данная константа в классе Mage_SalesRule_Model_Rule отсутствует.
	 * Для совместимости с Magento 1.4.0.1 определяем константу здесь.
	 */
	const BY_PERCENT_ACTION = 'by_percent';
	/** @return Df_SalesRule_Model_Resource_Rule_Collection */
	public static function c() {return new Df_SalesRule_Model_Resource_Rule_Collection;}
	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}