<?php
class Df_PromoGift_Model_Resource_Gift_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {
	/**
	 * Отбраковываем неотносящиеся к магазину правила
	 *
	 * @param int $websiteId
	 * @return Df_PromoGift_Model_Resource_Gift_Collection
	 */
	public function addWebsiteFilter($websiteId) {
		$this
			->addFieldToFilter(
				$this->getSelect()->getAdapter()
					->quoteIdentifier(Df_PromoGift_Const::DB__PROMO_GIFT__WEBSITE_ID)
				,array(Df_Varien_Const::EQ => $websiteId)
			)
		;
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