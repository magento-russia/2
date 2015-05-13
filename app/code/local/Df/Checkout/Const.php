<?php
interface Df_Checkout_Const {
	const ADDRESS_FORM_TYPE__SHIPPING = 'shipping';
	const ADDRESS_FORM_TYPE__BILLING = 'billing';
	const URL__CART = 'checkout/cart';
	const URL__CHECKOUT = 'checkout/onepage';
	const SESSION_CLASS_MF = 'checkout/session';
	const SESSION_PARAM__LAST_ORDER_ID = 'last_order_id';
	const SESSION_PARAM__LAST_REAL_ORDER_ID = 'last_real_order_id';
	const SESSION_PARAM__LAST_SUCCESS_QUOTE_ID = 'last_success_quote_id';
}