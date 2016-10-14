<?php
/**
 * @used-by Df_IPay_Model_Action_Abstract::order()
 * @param Df_Core_Model_StoreM|int|string|bool|null $store [optional]
 * @return string
 */
function rm_current_domain($store = null) {
	/** @var string $baseUrl */
	$baseUrl = rm_store($store)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
	return Zend_Uri_Http::fromString($baseUrl)->getHost();
}