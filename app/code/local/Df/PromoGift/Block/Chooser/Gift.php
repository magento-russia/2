<?php
class Df_PromoGift_Block_Chooser_Gift extends Df_Core_Block_Template_NoCache {
	/** @return Df_PromoGift_Model_Gift */
	public function getGift() {return $this->_gift;}

	/**
	 * @param Mage_Catalog_Model_Product $product
	 * @param string $template
	 * @return string
	 */
	public function renderProduct(Mage_Catalog_Model_Product $product, $template) {
		df_param_string($template, 1);
		/** @var Df_PromoGift_Block_Chooser_Product $block */
		$block = Df_PromoGift_Block_Chooser_Product::i();
		$block->setProduct($product);
		$block->setTemplate($template);
		return $block->renderView();
	}

	/**
	 * @param Df_PromoGift_Model_Gift $gift
	 * @return Df_PromoGift_Block_Chooser_Gift
	 */
	public function setGift(Df_PromoGift_Model_Gift $gift) {
		$this->_gift = $gift;
		return $this;
	}
	/** @var Df_PromoGift_Model_Gift */
	private $_gift;

	/** @return Df_PromoGift_Block_Chooser_Gift */
	public static function i() {return df_block(__CLASS__);}
}