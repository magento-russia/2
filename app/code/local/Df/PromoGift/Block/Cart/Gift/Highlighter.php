<?php
class Df_PromoGift_Block_Cart_Gift_Highlighter extends Df_Core_Block_Template_NoCache {
	/** @return array */
	public function getGiftingQuoteItemIds() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_h()->promoGift()->getCustomerRuleCounter()->getGiftingQuoteItemIds();
		}
		return $this->{__METHOD__};
	}
	const _C = __CLASS__;
}