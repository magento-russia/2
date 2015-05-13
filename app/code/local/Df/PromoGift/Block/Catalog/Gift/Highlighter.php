<?php
class Df_PromoGift_Block_Catalog_Gift_Highlighter extends Df_Core_Block_Template_NoCache {
	const _CLASS = __CLASS__;

	/** @return int[] */
	public function getEligibleProductIds() {
		if (!isset($this->{__METHOD__})) {
			$result = array();
			foreach (df_h()->promoGift()->getApplicablePromoActions() as $promoAction) {
				/** @var Df_PromoGift_Model_PromoAction $promoAction */
				foreach ($promoAction->getGifts() as $gift) {
					/** @var Df_PromoGift_Model_Gift $gift */
					$result[]= $gift->getProductId();
				}
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}
}