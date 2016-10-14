<?php
class Df_PromoGift_Block_Chooser_Gift extends Df_Core_Block_Template_NoCache {
	/** @return Df_PromoGift_Model_Gift */
	public function getGift() {return $this[self::$P__GIFT];}

	/** @var string */
	private static $P__GIFT = 'gift';

	/**
	 * @used-by df/promo_gift/chooser/center/promo-action.phtml
	 * @used-by df/promo_gift/chooser/side/promo-action.phtml
	 * @param Df_PromoGift_Model_Gift $gift
	 * @param string $template
	 * @return string
	 */
	public static function r(Df_PromoGift_Model_Gift $gift, $template) {
		return df_render(new self(array(
			self::$P__GIFT => $gift, 'template' => "df/promo_gift/chooser/{$template}/gift.phtml"
		)));
	}
}