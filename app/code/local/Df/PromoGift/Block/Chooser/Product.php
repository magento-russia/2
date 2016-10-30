<?php
class Df_PromoGift_Block_Chooser_Product extends Df_Core_Block_Template_NoCache {
	/** @return string */
	public function getButtonCaption() {
		return
			df_cfgr()->promotion()->gifts()->enableAddToCartButton()
			? df_mage()->catalogHelper()->__('Add to Cart')
			: $this->__('Подробнее...')
		;
	}

	/** @return string */
	public function getButtonTitle() {
		return
			df_cfgr()->promotion()->gifts()->enableAddToCartButton()
			? df_mage()->catalogHelper()->__('Add to Cart')
			: $this->getName()
		;
	}

	/** @return string */
	public function getButtonUrl() {
		return
			df_cfgr()->promotion()->gifts()->enableAddToCartButton()
			? df_mage()->checkout()->cartHelper()->getAddUrl($this->getProduct())
			: $this->getDetailsUrl()
		;
	}

	/**
	 * Возвращает название товара
	 * @return string
	 */
	public function getName() {return $this->getProduct()->getName();}

	/**
	 * Возвращает адрес товарной страницы
	 * @return string
	 */
	public function getDetailsUrl() {return $this->getProduct()->getProductUrl();}

	/** @return Df_Catalog_Model_Product */
	public function getProduct() {return $this[self::$P__PRODUCT ];}

	/**
	 * Возвращает адрес миниатюрной картинки товара
	 * @param int $size
	 * @return string
	 */
	public function getThumbnailUrl($size) {
		df_param_integer($size, 0);
		$this->getImageHelper()->init($this->getProduct(), 'small_image');
		return (string)$this->getImageHelper()->resize($size);
	}

	/** @return Mage_Catalog_Helper_Image */
	private function getImageHelper() {return df_mage()->catalogImageHelper();}

	/** @var string */
	private static $P__PRODUCT = 'product';

	/**
	 * @used-by df/promo_gift/chooser/center/gift.phtml
	 * @used-by df/promo_gift/chooser/side/gift.phtml
	 * @param Df_Catalog_Model_Product $product
	 * @param string $template
	 * @return string
	 */
	public static function r(Df_Catalog_Model_Product $product, $template) {
		return df_render(new self(array(
			self::$P__PRODUCT => $product, 'template' => "df/promo_gift/chooser/{$template}/product.phtml"
		)));
	}
}