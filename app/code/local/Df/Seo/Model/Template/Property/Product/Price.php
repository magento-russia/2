<?php
class Df_Seo_Model_Template_Property_Product_Price extends Df_Seo_Model_Template_Property_Product {
	/** @return string */
	public function getValue() {
		$result =
			/**
			 * Раньше тут стояло strip_tags($this->getProduct()->getFormatedPrice()),
			 * что работало неправильно при отличии учетной валюты от витринной:
			 * @link http://magento-forum.ru/topic/3699/
			 */
			Mage_Core_Helper_Data::currency(
				$value =
					df_mage()->taxHelper()->getPrice(
						$product = $this->getProduct()
						/**
						 * Обратите внимание,
						 * что надо использовать именно метод
						 * @see Mage_Catalog_Model_Product_Type_Price::getFinalPrice()
						 * или @see Mage_Catalog_Model_Product_Type_Price::getBasePrice()
						 * вместо @see Mage_Catalog_Model_Product::getPrice(),
						 * чтобы учитывать скидки, специальные цены и т.п.
						 * @link http://magento-forum.ru/topic/3705/
						 */
						,$price = $this->getProduct()->getCompositeFinalPriceWithTax()
					)
				,$format = true
				,$includeContainer = false
			)
		;
		return $result;
	}

	const _CLASS = __CLASS__;
}