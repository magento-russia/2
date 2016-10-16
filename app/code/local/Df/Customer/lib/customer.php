<?php
/**
 * 2016-10-16
 * @return Mage_Customer_Helper_Data
 */
function df_customer_h() {return Mage::helper('customer');}

/** @return Mage_Customer_Model_Session|Df_Customer_Model_Session */
function df_session_customer() {return Mage::getSingleton('customer/session');}

