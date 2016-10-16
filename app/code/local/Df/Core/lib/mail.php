<?php
use Mage_Core_Model_Store as Store;
/**
 * @param int|string|null|bool|Store $store [optional]
 * @return string
 */
function df_store_mail_address($store = null) {return dfcf(function($store = null) {
	/** @var string $result */
	$result = df_nts(Mage::getStoreConfig('trans_email/ident_general/email', $store));
	/** @var Zend_Validate_EmailAddress $mailValidator */
	$mailValidator = new Zend_Validate_EmailAddress();
	if (!$mailValidator->isValid($result)) {
		$result = 'noname@' . df_current_domain($store);
	}
	df_result_string_not_empty($result);
	df_assert($mailValidator->isValid($result));
	return $result;
}, func_get_args());}

