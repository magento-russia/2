<?php
class Df_PromoGift_Helper_Data extends Mage_Core_Helper_Abstract {
	/** @return Df_PromoGift_Model_Customer_Rule_Counter */
	public function getCustomerRuleCounter() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_PromoGift_Model_Customer_Rule_Counter::i();
		}
		return $this->{__METHOD__};
	}

	/** @return Df_PromoGift_Model_PromoAction_Collection */
	public function getApplicablePromoActions() {
		if (!isset($this->{__METHOD__}))  {
			$this->{__METHOD__} = Df_PromoGift_Model_PromoAction_Collection::i();
			// Иначе ioncube работает некорректно
			$this->{__METHOD__}->loadData();
		}
		return $this->{__METHOD__};
	}

	const _C = __CLASS__;

	/** @return Df_PromoGift_Helper_Data */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}