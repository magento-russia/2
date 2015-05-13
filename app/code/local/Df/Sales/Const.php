<?php
interface Df_Sales_Const {
	const ORDER_STATUS_HISTORY_PARAM__IS_CUSTOMER_NOTIFIED = 'is_customer_notified';
	const ORDER_STATUS_HISTORY_PARAM__IS_VISIBLE_ON_FRONT = 'is_visible_on_front';
	const ORDER_PARAM__CUSTOMER_LASTNAME = 'customer_lastname';
	const ORDER_PARAM__CUSTOMER_FIRSTNAME = 'customer_firstname';
	const ORDER_PARAM__CUSTOMER_MIDDLENAME = 'customer_middlename';
	const ORDER_PARAM__INCREMENT_ID = 'increment_id';
	const ORDER_PARAM__IS_CUSTOMER_NOTIFIED = 'is_customer_notified';
	const ORDER_PARAM__PROTECT_CODE = 'protect_code';
	const ORDER_ADDRESS_CLASS = 'Mage_Sales_Model_Order_Address';
	const ORDER_ADDRESS_CLASS_MF = 'sales/order_address';
	const ORDER_ADDRESS__PARAM__CITY = 'city';
	const ORDER_ADDRESS__PARAM__FIRSTNAME = 'firstname';
	const ORDER_ADDRESS__PARAM__LASTNAME = 'lastname';
	const ORDER_ADDRESS__PARAM__MIDDLENAME = 'middlename';
	const ORDER_ADDRESS__PARAM__POSTCODE = 'postcode';
	const ORDER_ADDRESS__PARAM__STREET = 'street';
	const QUOTE_CLASS = 'Mage_Sales_Model_Quote';
	const QUOTE_ADDRESS_CLASS = 'Mage_Sales_Model_Quote_Address';
	const QUOTE_CLASS_MF = 'sales/quote';
	const QUOTE_ITEM_CLASS = 'Mage_Sales_Model_Quote_Item';
	const QUOTE_ITEM_CLASS_MF = 'sales/quote_item';
	const QUOTE_ITEM_ABSTRACT_CLASS = 'Mage_Sales_Model_Quote_Item_Abstract';
	const QUOTE_ITEM_OPTION_CLASS = 'Mage_Sales_Model_Quote_Item_Option';
	const QUOTE_ITEM_OPTION_CLASS_MF = 'sales/quote_item_option';
}