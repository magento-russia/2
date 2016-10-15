<?php
/**
 * 2015-04-14
 * @used-by Df_Tax_Model_Resource_Class_Collection::filterByShopCountry()
 * @used-by Df_Tax_Model_Resource_Class_Collection::filterByShopCountry()
 * @param Df_Core_Model_StoreM|int|string|bool|null $store [optional]
 * @return string|null
 */
function rm_shop_iso2($store = null) {
	/**
	 * 2015-08-09
	 * Константа @see Mage_Core_Helper_Data::XML_PATH_MERCHANT_COUNTRY_CODE
	 * отсутствует в Magento CE 1.4.0.1:
	 * https://github.com/OpenMage/magento-mirror/blob/1.4.0.1/app/code/core/Mage/Core/Helper/Data.php
	 */
	return Mage::getStoreConfig('general/store_information/merchant_country', df_store($store));
}

