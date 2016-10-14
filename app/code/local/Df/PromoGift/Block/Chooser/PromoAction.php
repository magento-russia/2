<?php
class Df_PromoGift_Block_Chooser_PromoAction extends Df_Core_Block_Template_NoCache {
	/** @return string */
	public function getDescription() {
		return df_nts($this->getPromoAction()->getRule()->getData('description'));
	}

	/** @return Df_Varien_Data_Collection */
	public function getGifts() {return $this->getPromoAction()->getGifts();}

	/** @return Df_PromoGift_Model_PromoAction */
	public function getPromoAction() {return $this[self::$P__PROMO_ACTION];}

	/** @var string */
	private static $P__PROMO_ACTION = 'promo_action';

	/**
	 * @used-by df/promo_gift/chooser/center/main.phtml
	 * @used-by df/promo_gift/chooser/side/main.phtml
	 * @param Df_PromoGift_Model_PromoAction $promoAction
	 * @param $template
	 * @return string
	 */
	public static function r(Df_PromoGift_Model_PromoAction $promoAction, $template) {
		return rm_render(new self(array(
			self::$P__PROMO_ACTION => $promoAction
			, 'template' => "df/promo_gift/chooser/{$template}/promo-action.phtml"
		)));
	}
}