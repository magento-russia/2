<?php
class Df_PageCache_Model_Container_CatalogProductList
	extends Df_PageCache_Model_Container_Advanced_Quote
{
	/**
	 * Render block that was not cached
	 *
	 * @return false|string
	 */
	protected function _renderBlock()
	{
		$productId = $this->_getProductId();
		if ($productId && !Mage::registry('product')) {
			/** @var Df_Catalog_Model_Product $product */
			$product = Mage::getModel('catalog/product');
			$product->setStoreId(Mage::app()->getStore()->getId());
			$product->load($productId);
			if ($product) {
				Mage::register('product', $product);
			}
		}

		if (Mage::registry('product')) {
			$block = $this->_getPlaceHolderBlock();
			Mage::dispatchEvent('render_block', array('block' => $block, 'placeholder' => $this->_placeholder));
			return $block->toHtml();
		}

		return '';
	}
}
