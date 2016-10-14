<?php
/** @return bool */
function rm_checkout_ergonomic() {
	return df_cfg()->checkout()->_interface()->needShowAllStepsAtOnce();
}

/** @return bool */
function rm_customer_logged_in() {return rm_session_customer()->isLoggedIn();}

/**
 * @param Df_Core_Destructable $object
 * @return void
 */
function rm_destructable_singleton(Df_Core_Destructable $object) {
	Df_Core_GlobalSingletonDestructor::s()->register($object);
}

/* @return Mage_Core_Model_Design_Package */
function rm_design_package() {return Mage::getSingleton('core/design_package');}

/**
 * @param Exception|string $e
 * @return string
 */
function rm_ets($e) {
	return
		is_string($e)
		? $e
		: ($e instanceof Df_Core_Exception ? $e->getMessageRm() : $e->getMessage())
	;
}

/** @return Df_Core_Model_Units_Length */
function rm_length() {return Df_Core_Model_Units_Length::s();}

/** @return Df_Localization_Settings_Area */
function rm_loc() {static $r; return $r ? $r : $r = Df_Localization_Settings::s()->current();}

/**
 * @param float|int|string $amount
 * @return Df_Core_Model_Money
 */
function rm_money($amount) {return Df_Core_Model_Money::i($amount); }

/**
 * @used-by rm_quote()
 * @return Mage_Checkout_Model_Session
 */
function rm_session_checkout() {return Mage::getSingleton('checkout/session');}

/** @return Mage_Core_Model_Session */
function rm_session_core() {return Mage::getSingleton('core/session');}

/** @return Mage_Customer_Model_Session */
function rm_session_customer() {return Mage::getSingleton('customer/session');}

/** @return Mage_Tax_Helper_Data */
function rm_tax_h() {static $r; return $r ? $r : $r = Mage::helper('tax');}

/** @return Df_Core_Model_Units_Weight */
function rm_weight() {return Df_Core_Model_Units_Weight::s();}


