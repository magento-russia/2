<?php
class Df_PromoGift_Block_Chooser_PromoAction extends Df_Core_Block_Template_NoCache {
	/** @return string */
	public function getDescription() {
		return df_nts($this->getPromoAction()->getRule()->getData('description'));
	}

	/** @return Df_Varien_Data_Collection */
	public function getGifts() {
		return $this->getPromoAction()->getGifts();
	}

	/** @return Df_PromoGift_Model_PromoAction */
	public function getPromoAction() {
		return $this->_promoAction;
	}

	/**
	 * @param Df_PromoGift_Model_Gift $gift
	 * @param string $template
	 * @return string
	 */
	public function renderGift(Df_PromoGift_Model_Gift $gift, $template) {
		df_param_string($template, 1);
		/** @var Df_PromoGift_Block_Chooser_Gift $block */
		$block = Df_PromoGift_Block_Chooser_Gift::i();
		$block->setGift($gift);
		$block->setTemplate($template);
		return $block->renderView();
	}

	/**
	 * @param Df_PromoGift_Model_PromoAction $promoAction
	 * @return Df_PromoGift_Block_Chooser_PromoAction
	 */
	public function setPromoAction(Df_PromoGift_Model_PromoAction $promoAction) {
		$this->_promoAction = $promoAction;
		return $this;
	}
	/** @var Df_PromoGift_Model_PromoAction */
	private $_promoAction;

	/** @return Df_PromoGift_Block_Chooser_PromoAction */
	public static function i() {return df_block(__CLASS__);}
}