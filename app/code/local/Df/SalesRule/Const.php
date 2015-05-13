<?php
/**
 * Константы модуля
 */
interface Df_SalesRule_Const {
	/**
	 * В Magento 1.5.0.1 данная константа определена в классе Mage_SalesRule_Model_Rule.
	 * В Magento 1.4.0.1 данна константа в классе Mage_SalesRule_Model_Rule отсутствует.
	 * Для совместимости с Magento 1.4.0.1 определяем константу здесь.
	 */
	const BY_PERCENT_ACTION = 'by_percent';
	const RULE_CLASS = 'Mage_SalesRule_Model_Rule';
	const RULE_CLASS_MF = 'salesrule/rule';
	const RULE_COLLECTION_CLASS = 'Mage_SalesRule_Model_Mysql4_Rule_Collection';
	const RULE_EVENT_PARAM = 'rule';
	const RULE_ENTITY = 'salesrule_rule';
	const DB__SALESRULE__IS_ACTIVE = 'is_active';
	const DB__SALESRULE__DISCOUNT_AMOUNT = 'discount_amount';
	const DB__SALESRULE__SIMPLE_ACTION = 'simple_action';
	const DB__SALESRULE__TO_DATE = 'to_date';
	const DB__SALESRULE__FROM_DATE = 'from_date';
	const DB__SALESRULE__WEBSITE_IDS = 'website_ids';
	const DB__SALES_RULE__USES_PER_CUSTOMER = 'uses_per_customer';
	const DB__SALES_RULE_CUSTOMER__TIMES_USED = 'times_used';
	const RULE_IS_VALID = 'is_valid';
}