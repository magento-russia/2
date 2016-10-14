<?php
define('RM_URL_CHECKOUT', 'checkout/onepage');

/**
 * @param bool $throw [optional]
 * @return Df_Sales_Model_Order|null
 */
function df_last_order($throw = true) {
	static $r; return $r ? rm_n_get($r) : $r = rm_n_set(
		Df_Sales_Model_Order::ldi(df_last_order_iid(), $throw)
	);
}

/**
 * 2015-03-14
 * В настоящее время эта функция никем не используется, она здесь только для справки.
 * Вместо этой функции все используют функцию @see df_last_order_iid().
 * Обратите внимание,
 * что ядро Magento инициалиализирует поля «last_order_id» и «last_real_order_id» всегда парно,
 * и функции @see df_last_order_id() и @see df_last_order_iid() возвращают идентфиикаторы одного и того же заказа,
 * просто @see  df_last_order_id() возвращает короткий целочисленный идентификатор,
 * а df_last_order_iid() — длинный символьный (хотя тоже состоящий, в основном, из цифр).
 * @return int|null
 */
function df_last_order_id() {return rm_session_checkout()->getData('last_order_id');}

/** @return string|null */
function df_last_order_iid() {return rm_session_checkout()->getData('last_real_order_id');}

/** @return void */
function rm_redirect_to_checkout() {rm_controller()->setRedirectWithCookieCheck('checkout/onepage');}

/**
 * 2015-03-31
 * @used-by rm_quote_address_billing()
 * @used-by rm_quote_address_shipping()
 * @used-by rm_quote_has_items()
 * @used-by Df_Checkout_Block_Cart_Sidebar::getCacheKeyInfo()
 * @used-by Df_CustomerBalance_Block_Checkout_Payment::_getQuote()
 * @used-by Df_Pbridge_Block_Checkout_Payment_Review_Container::_toHtml()
 * @used-by Df_Pbridge_Helper_Data::getReviewButtonTemplate()
 * @used-by Df_PromoGift_Model_Rule::isApplicableToQuote()
 * @used-by Df_PromoGift_Model_Rule::isTheCustomerAlreadyGotMaxGiftsByThisRuleDuringPrevoiusCheckouts()
 * @used-by Df_Reward_Block_Checkout_Payment::isEnoughPoints()
 * @used-by Df_Reward_Block_Checkout_Payment::useRewardPoints()
 * @used-by Df_Reward_Block_Tooltip_Checkout::initRewardType()
 * @used-by Df_Reward_CartController::removeAction()
 * @used-by Df_CustomerBalance_Block_Checkout_Payment::getAmountToCharge()
 * @used-by Df_CustomerBalance_Block_Checkout_Payment::isCustomerBalanceUsed()
 * @used-by Df_CustomerBalance_Block_Checkout_Payment::isFullyPaidAfterApplication()
 * @used-by df/customerbalance/checkout/payment/after.phtml
 * @used-by df/customerbalance/checkout/payment/before.phtml
 * @used-by df/customerbalance/checkout/payment/multishipping.phtml
 * @used-by df/reward/checkout/payment/after.phtml
 * @used-by df/reward/checkout/payment/before.phtml
 * @used-by df/reward/checkout/payment/multishipping.phtml
 * @return Mage_Sales_Model_Quote|Df_Sales_Model_Quote
 */
function rm_quote() {return rm_session_checkout()->getQuote();}

/**
 * 2015-03-31
 * Обратите внимание, что эта функция не кэширует свой результат
 * и в то же время является ресурсоёмкой,
 * ибо @uses Mage_Sales_Model_Quote::_getAddressByType() использует foreach
 * @used-by Df_Checkout_Block_Frontend_Ergonomic_Address::getAddress()
 * @used-by Df_Customer_Model_Form::getAddress()
 * @used-by Df_Qiwi_Block_Form::getBillingAddressPhone()
 * @return Df_Sales_Model_Quote_Address
 */
function rm_quote_address_billing() {return rm_quote()->getBillingAddress();}

/**
 * 2015-03-31
 * Обратите внимание, что эта функция не кэширует свой результат
 * и в то же время является ресурсоёмкой,
 * ибо @uses Mage_Sales_Model_Quote::_getAddressByType() использует foreach
 * @used-by Df_Checkout_Block_Frontend_Ergonomic_Address::getAddress()
 * @used-by Df_Customer_Model_Form::getAddress()
 * @used-by Df_Qiwi_Block_Form::getShippingAddressPhone()
 * @used-by df/checkout/ergonomic/dashboard.phtml
 * @return Df_Sales_Model_Quote_Address
 */
function rm_quote_address_shipping() {return rm_quote()->getShippingAddress();}

/**
 * 2015-03-31
 * @used-by Df_Catalog_Block_Product_List_Related::_construct()
 * @used-by Df_Catalog_Block_Product_List_Upsell::_construct()
 * @return bool
 */
function rm_quote_has_items() {
	df_module_enabled('Mage_Checkout')
	&& rm_session_checkout()->getQuoteId()
	&& rm_quote()->getItemsCount();
}

/**
 * Без _nosid система будет формировать ссылку вида
 * http://localhost.com:656/df-payment/cancel/?___SID=U,
 * и тогда, в частности, Единая Касса неверно вычисляет ЭЦП.
 * @used-by Df_Payment_Model_Action_Confirm::redirectToFail()
 * @used-by Df_LiqPay_CustomerReturnController::processFailure()
 * @used-by Df_IPay_CustomerReturnController::indexAction()
 * @return string
 */
function rm_url_checkout_fail() {
	static $r; return $r ? $r : $r = Mage::getUrl('df-payment/cancel', array('_nosid' => true));
}

/**
 * Без _nosid система будет формировать ссылку вида
 * http://localhost.com:656/checkout/onepage/success/?___SID=U,
 * и тогда, в частности, Единая Касса неверно вычисляет ЭЦП.
 * @used-by Df_Payment_Model_Action_Confirm::redirectToSuccess()
 * @used-by Df_LiqPay_CustomerReturnController::processSuccess()
 * @used-by Df_IPay_CustomerReturnController::indexAction()
 * @return string
 */
function rm_url_checkout_success() {
	static $r; return $r ? $r : $r = Mage::getUrl('checkout/onepage/success', array('_nosid' => true));
}

