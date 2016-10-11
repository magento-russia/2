<?php
/** @return Mage_Core_Model_Session_Abstract */
function rm_session() {
	/** @var Mage_Core_Model_Session_Abstract $result */
	static $result;
	if (!isset($result)) {
		$result = df_is_admin() ? df_mage()->adminhtml()->session() : rm_session_core();
		df_assert($result instanceof Mage_Core_Model_Session_Abstract);
	}
	return $result;
}

/**
 * 2016-10-11
 * @return Mage_Admin_Model_Session
 */
function rm_session_admin() {return Mage::getSingleton('admin/session');}

/* @return Mage_Checkout_Model_Session */
function rm_session_checkout() {return Mage::getSingleton('checkout/session');}

/* @return Mage_Core_Model_Session */
function rm_session_core() {return Mage::getSingleton('core/session');}

/* @return Mage_Customer_Model_Session */
function rm_session_customer() {return Mage::getSingleton('customer/session');}