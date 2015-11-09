<?php
class Df_PromoGift_Model_Resource_Rule_Collection
	extends Mage_SalesRule_Model_Mysql4_Rule_Collection {
	protected function _beforeLoad() {
		/** @var Varien_Db_Select $select */
		$select = $this->getSelect();
		/** @var Zend_Db_Adapter_Abstract $adapter */
		$adapter = $select->getAdapter();
		// Отбраковываем отключенные правила
		/**
		 * 2015-11-09
		 * Убрал вызов @see Zend_Db_Adapter_Abstract::quoteIdentifier()
		 * для совместимости с Magento CE 1.9.2.2,
		 * потому что эта версия по соображениям безопасности магазина
		 * после установки неряшливо написанных сторонних модулей
		 * сама добавляет кавычки ко всем полям, указанным в методе
		 * @uses Varien_Data_Collection_Db::addFieldToFilter(),
		 * и когда качественно написанный модуль добавляет свои кавычки,
		 * то получается, что ядро, в угоду неряшливо написанным модулям
		 * бездумно добавляет дополнительные кавычки,
		 * и в командах SQL имена полей получаются некорректными, например: AND (```is_active``` = 1)
		 * @see Varien_Data_Collection_Db::_translateCondition():
				$quotedField = $this->getConnection()->quoteIdentifier($field);
		 * https://github.com/OpenMage/magento-mirror/blob/92a1142a37a1f8f639db95353199368f5784725d/lib/Varien/Data/Collection/Db.php#L417
		 */
		$this->addFieldToFilter(Df_SalesRule_Const::DB__SALESRULE__IS_ACTIVE, array(Df_Varien_Const::EQ => 1));
		// Нас интересуют только правила типа «Percent of product price discount»,
		// потому что именно такими правилами определяются промо-подарки
		$this->addFieldToFilter(Df_SalesRule_Const::DB__SALESRULE__SIMPLE_ACTION
			,array(Df_Varien_Const::EQ => Df_SalesRule_Const::BY_PERCENT_ACTION)
		);
		/**
		 * Выбираем правила, дающую 100-процентную скидку
		 * (другими словами, предоставляющие товары в поларок)
		 */
		$this->addFieldToFilter(
			Df_SalesRule_Const::DB__SALESRULE__DISCOUNT_AMOUNT, array(Df_Varien_Const::EQ => 100)
		);
		/**
		 * Отбраковываем правила с истёкшим сроком действия.
		 * Обратите внимание, что правила, недействующие сейчас, но запланированные на будущее, * мы не отбраковываем, потому что они могут стать действующими
		 * в период между данным процессом (индексацией) и покупкой.
		 */
		$this->addFieldToFilter(Df_SalesRule_Const::DB__SALESRULE__TO_DATE, array(
			 Df_Varien_Const::_OR => array(
				array(Df_Varien_Const::FROM => Zend_Date::now(), Df_Varien_Const::DATETIME => true)
				,array(Df_Varien_Const::IS => new Zend_Db_Expr (Df_Zf_Const::NULL))
			)
		));
		parent::_beforeLoad();
	}

	/**
	 * Отбраковываем ещё не начавшиеся правила
	 * @return Df_PromoGift_Model_Resource_Rule_Collection
	 */
	public function addNotStartedYetRulesExclusionFilter() {
		/**
		 * 2015-11-09
		 * Убрал вызов @see Zend_Db_Adapter_Abstract::quoteIdentifier()
		 * для совместимости с Magento CE 1.9.2.2,
		 * потому что эта версия по соображениям безопасности магазина
		 * после установки неряшливо написанных сторонних модулей
		 * сама добавляет кавычки ко всем полям, указанным в методе
		 * @uses Varien_Data_Collection_Db::addFieldToFilter(),
		 * и когда качественно написанный модуль добавляет свои кавычки,
		 * то получается, что ядро, в угоду неряшливо написанным модулям
		 * бездумно добавляет дополнительные кавычки,
		 * и в командах SQL имена полей получаются некорректными, например: AND (```is_active``` = 1)
		 * @see Varien_Data_Collection_Db::_translateCondition():
				$quotedField = $this->getConnection()->quoteIdentifier($field);
		 * https://github.com/OpenMage/magento-mirror/blob/92a1142a37a1f8f639db95353199368f5784725d/lib/Varien/Data/Collection/Db.php#L417
		 */
		$this->addFieldToFilter(Df_SalesRule_Const::DB__SALESRULE__FROM_DATE, array(
			 Df_Varien_Const::_OR => array(
				array(Df_Varien_Const::TO => Zend_Date::now(), Df_Varien_Const::DATETIME => true)
				,array(Df_Varien_Const::IS => new Zend_Db_Expr (Df_Zf_Const::NULL))
			)
		));
		return $this;
	}
	const _CLASS =  __CLASS__;
	/** @return Df_PromoGift_Model_Resource_Rule_Collection */
	public static function i() {return new self;}
}