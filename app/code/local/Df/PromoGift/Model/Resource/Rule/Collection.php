<?php
class Df_PromoGift_Model_Resource_Rule_Collection extends Df_SalesRule_Model_Resource_Rule_Collection {
	/**
	 * @override
	 * @see Mage_Core_Model_Resource_Db_Collection_Abstract::_beforeLoad()
	 * @return Df_PromoGift_Model_Resource_Rule_Collection
	 */
	protected function _beforeLoad() {
		// Отбраковываем отключенные правила
		$this->addFieldToFilter(Df_SalesRule_Model_Rule::P__IS_ACTIVE, array('eq' => 1));
		// Нас интересуют только правила типа «Percent of product price discount»,
		// потому что именно такими правилами определяются промо-подарки.
		$this->addFieldToFilter('simple_action', 'by_percent');
		// Выбираем правила, дающую 100-процентную скидку
		// (другими словами, предоставляющие товары в поларок)
		$this->addFieldToFilter(Df_SalesRule_Model_Rule::P__DISCOUNT_AMOUNT, array('eq' => 100));
		// Отбраковываем правила с истёкшим сроком действия.
		// Обратите внимание, что правила, недействующие сейчас, но запланированные на будущее,
		// мы не отбраковываем, потому что они могут стать действующими
		// в период между данным процессом (индексацией) и покупкой.
		$this->addFieldToFilter(Df_SalesRule_Model_Rule::P__TO_DATE, array('or' => array(
			array('from' => Zend_Date::now(), 'datetime' => true)
			,array('is' => new Zend_Db_Expr(Df_Zf_Const::NULL))
		)));
		parent::_beforeLoad();
		return $this;
	}

	/**
	 * Отбраковываем ещё не начавшиеся правила
	 * @return Df_PromoGift_Model_Resource_Rule_Collection
	 */
	public function addNotStartedYetRulesExclusionFilter() {
		$this->addFieldToFilter(Df_SalesRule_Model_Rule::P__FROM_DATE, array('or' => array(
			array('to' => Zend_Date::now(), 'datetime' => true)
			,array('is' => new Zend_Db_Expr(Df_Zf_Const::NULL))
		)));
		return $this;
	}

	/** @return void */
	public function filterByCurrentQuote() {
		$this->load();
		$this->_items =
			dfa_select($this->_items, array_keys(array_filter($this->walk('isApplicableToQuote'))))
		;
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_itemObjectClass = Df_PromoGift_Model_Rule::class;
	}

	/** @return Df_PromoGift_Model_Resource_Rule_Collection */
	public static function i() {return new self;}
}