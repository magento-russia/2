<?php
class Df_PromoGift_Model_Resource_Gift_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {
	/**
	 * Отбраковываем неотносящиеся к магазину правила
	 *
	 * @param int $websiteId
	 * @return Df_PromoGift_Model_Resource_Gift_Collection
	 */
	public function addWebsiteFilter($websiteId) {
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
		$this->addFieldToFilter(
			Df_PromoGift_Const::DB__PROMO_GIFT__WEBSITE_ID, array(Df_Varien_Const::EQ => $websiteId)
		);
		return $this;
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_init(Df_PromoGift_Model_Gift::mf(), Df_PromoGift_Model_Resource_Gift::mf());
	}
	/** @var string */
	protected $_eventObject = 'gift_collection';
	/** @var string */
	protected $_eventPrefix = 'df_promo_gift_gift_collection';
	const _CLASS = __CLASS__;
	/** @return Df_PromoGift_Model_Resource_Gift_Collection */
	public static function i() {return new self;}
}