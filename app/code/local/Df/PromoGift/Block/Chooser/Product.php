<?php
class Df_PromoGift_Block_Chooser_Product extends Df_Core_Block_Template_NoCache {
	/** @return string */
	public function getButtonCaption() {
		return
			df_cfg()->promotion()->gifts()->enableAddToCartButton()
			? df_mage()->catalogHelper()->__('Add to Cart')
			: $this->__('Подробнее...')
		;
	}

	/** @return string */
	public function getButtonTitle() {
		return
			df_cfg()->promotion()->gifts()->enableAddToCartButton()
			? df_mage()->catalogHelper()->__('Add to Cart')
			: $this->getName()
		;
	}

	/** @return string */
	public function getButtonUrl() {
		return
			df_cfg()->promotion()->gifts()->enableAddToCartButton()
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
	public function getProduct() {return $this->_product;}

	/**
	 * @param Df_Catalog_Model_Product $product
	 * @return Df_PromoGift_Block_Chooser_Product
	 */
	public function setProduct(Df_Catalog_Model_Product $product) {
		$this->_product = $product;
		return $this;
	}
	/** @var Df_Catalog_Model_Product */
	private $_product;

	/**
	 * Возвращает адрес миниатюрной картинки товара
	 * @param int $size
	 * @return string
	 */
	public function getThumbnailUrl($size) {
		df_param_integer($size, 0);
		$this->getImageHelper()->init($this->getProduct(), self::SMALL_IMAGE);
		return (string)$this->getImageHelper()->resize($size);
	}

	/** @return Mage_Catalog_Helper_Image */
	private function getImageHelper() {return df_mage()->catalogImageHelper();}

	const SMALL_IMAGE = 'small_image';

	/** @return Df_PromoGift_Block_Chooser_Product */
	public static function i() {return df_block(__CLASS__);}
}