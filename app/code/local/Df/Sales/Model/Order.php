<?php
/**
 * @method float|null getBaseCustomerBalanceAmount()
 * @method float|null getBaseRewardCurrencyAmount()
 * @method float|null getBaseRewardCurrencyAmountInvoiced()
 * @method float|null getBaseRewardCurrencyAmountRefunded()
 * @method float|null getCustomerBalanceInvoiced()
 * @method float|null getCustomerBalanceRefunded()
 * @method float|null getRewardPointsBalance()
 * @method Df_Sales_Model_Order setForcedCanCreditmemo(bool $value)
 */
class Df_Sales_Model_Order extends Mage_Sales_Model_Order {
	/**
	 * @used-by Df_LiqPay_CustomerReturnController::processDelayed()
	 * @used-by \Df\Payment\Action::commentOrder()
	 * @used-by Df_Reward_Observer::applyRewardSalesrulePoints()
	 * @used-by Df_Reward_Observer::orderCompleted()
	 * @param string $comment
	 * @param bool $isCustomerNotified [optional]
	 * @return void
	 */
	public function comment($comment, $isCustomerNotified = false) {
		/** @var Mage_Sales_Model_Order_Status_History $history */
		$history = $this->addStatusHistoryComment($comment);
		$history->setIsCustomerNotified($isCustomerNotified);
		$history->save();
	}

	/**
	 * @override
	 * @param float $price
	 * @param bool $addBrackets
	 * @return string
	 */
	public function formatPrice($price, $addBrackets = false) {
		return
			df_loc()->needHideDecimals()
			? $this->formatPriceDf($price, $addBrackets)
			: parent::formatPrice($price, $addBrackets)
		;
	}

	/** @return string */
	public function getCreatedAt() {return $this->_getData(self::P__CREATED_AT);}
	
	/** @return Zend_Date */
	public function getDateCreated() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_date_from_db($this->getCreatedAt());
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return string|null
	 */
	public function getEmailCustomerNote() {
		/** @var bool $preserveLineBreaks */
		static $preserveLineBreaks;
		if (is_null($preserveLineBreaks)) {
			$preserveLineBreaks =
				df_cfgr()->sales()->orderComments()->preserveLineBreaksInOrderEmail()
			;
		}
		/** @var string|null $result */
		$result = parent::getEmailCustomerNote();
		if ($result && $preserveLineBreaks) {
			$result = df_t()->nl2br($result);
			if (df_cfgr()->sales()->orderComments()->wrapInStandardFrameInOrderEmail()) {
				$result = Df_Sales_Block_Order_Email_Comments::r($result);
			}
		}
		if (!is_null($result)) {
			df_result_string($result);
		}
		return $result;
	}

	/**
	 * @override
	 * @return Df_Sales_Model_Resource_Order_Collection
	 */
	public function getResourceCollection() {return self::c();}

	/** @return bool */
	public function needCommentToBeVisibleOnFront() {return $this->_commentNeedToBeVisibleOnFront;}

	/**
	 * @param bool $value
	 * @return void
	 */
	public function setCommentToBeVisibleOnFront($value) {$this->_commentNeedToBeVisibleOnFront = $value;}

	/**
	 * 2015-02-13
	 * Этот метод у нас перекрыт с самых первых версий Российской сборки Magento.
	 * При повторных сохранениях заказа прежнее значение protect_code
	 * заменяется новым в родительском методе @see Mage_Sales_Model_Order::_beforeSave():
	 * $this->setData('protect_code', substr(md5(uniqid(mt_rand(), true) . ':' . microtime(true)), 5, 6));
	 * Нам же нужно сохранять прежнее значение protect_code,
	 * потому что оно используется, например, в веб-адресе на квитанцию Сбербанка для конкретного заказа.
	 * @override
	 * @return Df_Sales_Model_Order
	 */
	protected function _beforeSave() {
		/** @var string|null $protectCode */
		$protectCode = $this->getProtectCode();
		parent::_beforeSave();
		/**
		 * Обратите внимание, что при первом сохранении заказа переменная $protectCode равна null
		 * (ведь protect_code инициализируется как раз при вызове parent::_beforeSave()).
		 */
		if ($protectCode) {
			$this->setProtectCode($protectCode);
		}
		return $this;
	}

	/**
	 * @override
	 * @return Df_Sales_Model_Resource_Order
	 * 2016-10-14
	 * В родительском классе метод переобъявлен через PHPDoc,
	 * и поэтому среда разработки думает, что он публичен.
	 */
	/** @noinspection PhpHierarchyChecksInspection */
	protected function _getResource() {return Df_Sales_Model_Resource_Order::s();}

	/**
	 * @param float $price
	 * @param bool $addBrackets
	 * @return string
	 */
	private function formatPriceDf($price, $addBrackets = false) {
		return $this->formatPricePrecision($price, df_currency_precision(), $addBrackets);
	}

	/** @var bool */
	private $_commentNeedToBeVisibleOnFront = false;
	/**
	 * @used-by Df_C1_Cml2_Export_Data_Entity_Customer::_construct()
	 * @used-by Df_C1_Cml2_Export_Processor_Sale_Order::_construct()
	 * @used-by \Df\Payment\Request\Payment::_construct()
	 * @used-by Df_Sales_Model_Resource_Order_Collection::_construct()
	 */

	const P__ADJUSTMENT_NEGATIVE = 'adjustment_negative';
	const P__ADJUSTMENT_POSITIVE = 'adjustment_positive';
	const P__APPLIED_RULE_IDS = 'applied_rule_ids';
	const P__BASE_ADJUSTMENT_NEGATIVE = 'base_adjustment_negative';
	const P__BASE_ADJUSTMENT_POSITIVE = 'base_adjustment_positive';
	const P__BASE_CURRENCY_CODE = 'base_currency_code';
	const P__BASE_CUSTOMER_BALANCE_AMOUNT = 'base_customer_balance_amount';
	const P__BASE_CUSTOMER_BALANCE_INVOICED = 'base_customer_balance_invoiced';
	const P__BASE_CUSTOMER_BALANCE_REFUNDED = 'base_customer_balance_refunded';
	const P__BASE_CUSTOMER_BALANCE_TOTAL_REFUNDED = 'base_customer_balance_total_refunded';
	const P__BASE_DISCOUNT_AMOUNT = 'base_discount_amount';
	const P__BASE_DISCOUNT_CANCELED = 'base_discount_canceled';
	const P__BASE_DISCOUNT_INVOICED = 'base_discount_invoiced';
	const P__BASE_DISCOUNT_REFUNDED = 'base_discount_refunded';
	const P__BASE_GRAND_TOTAL = 'base_grand_total';
	const P__BASE_HIDDEN_TAX_AMOUNT = 'base_hidden_tax_amount';
	const P__BASE_HIDDEN_TAX_INVOICED = 'base_hidden_tax_invoiced';
	const P__BASE_HIDDEN_TAX_REFUNDED = 'base_hidden_tax_refunded';
	const P__BASE_REWARD_CURRENCY_AMOUNT = 'base_reward_currency_amount';
	const P__BASE_REWARD_CURRENCY_AMOUNT_INVOICED = 'base_reward_currency_amount_invoiced';
	const P__BASE_REWARD_CURRENCY_AMOUNT_REFUNDED = 'base_reward_currency_amount_refunded';
	const P__BASE_SHIPPING_AMOUNT = 'base_shipping_amount';
	const P__BASE_SHIPPING_CANCELED = 'base_shipping_canceled';
	const P__BASE_SHIPPING_DISCOUNT_AMOUNT = 'base_shipping_discount_amount';
	const P__BASE_SHIPPING_HIDDEN_TAX_AMNT = 'base_shipping_hidden_tax_amnt';
	const P__BASE_SHIPPING_INCL_TAX = 'base_shipping_incl_tax';
	const P__BASE_SHIPPING_INVOICED = 'base_shipping_invoiced';
	const P__BASE_SHIPPING_REFUNDED = 'base_shipping_refunded';
	const P__BASE_SHIPPING_TAX_AMOUNT = 'base_shipping_tax_amount';
	const P__BASE_SHIPPING_TAX_REFUNDED = 'base_shipping_tax_refunded';
	const P__BASE_SUBTOTAL = 'base_subtotal';
	const P__BASE_SUBTOTAL_CANCELED = 'base_subtotal_canceled';
	const P__BASE_SUBTOTAL_INCL_TAX = 'base_subtotal_incl_tax';
	const P__BASE_SUBTOTAL_INVOICED = 'base_subtotal_invoiced';
	const P__BASE_SUBTOTAL_REFUNDED = 'base_subtotal_refunded';
	const P__BASE_TAX_AMOUNT = 'base_tax_amount';
	const P__BASE_TAX_CANCELED = 'base_tax_canceled';
	const P__BASE_TAX_INVOICED = 'base_tax_invoiced';
	const P__BASE_TAX_REFUNDED = 'base_tax_refunded';
	const P__BASE_TO_GLOBAL_RATE = 'base_to_global_rate';
	const P__BASE_TO_ORDER_RATE = 'base_to_order_rate';
	const P__BASE_TOTAL_CANCELED = 'base_total_canceled';
	const P__BASE_TOTAL_DUE = 'base_total_due';
	const P__BASE_TOTAL_INVOICED = 'base_total_invoiced';
	const P__BASE_TOTAL_INVOICED_COST = 'base_total_invoiced_cost';
	const P__BASE_TOTAL_OFFLINE_REFUNDED = 'base_total_offline_refunded';
	const P__BASE_TOTAL_ONLINE_REFUNDED = 'base_total_online_refunded';
	const P__BASE_TOTAL_PAID = 'base_total_paid';
	const P__BASE_TOTAL_QTY_ORDERED = 'base_total_qty_ordered';
	const P__BASE_TOTAL_REFUNDED = 'base_total_refunded';
	const P__BILLING_ADDRESS_ID = 'billing_address_id';
	const P__CAN_SHIP_PARTIALLY = 'can_ship_partially';
	const P__CAN_SHIP_PARTIALLY_ITEM = 'can_ship_partially_item';
	const P__COUPON_CODE = 'coupon_code';
	const P__COUPON_RULE_NAME = 'coupon_rule_name';
	const P__CREATED_AT = 'created_at';
	const P__CUSTOMER_BALANCE_AMOUNT = 'customer_balance_amount';
	const P__CUSTOMER_BALANCE_INVOICED = 'customer_balance_invoiced';
	const P__CUSTOMER_BALANCE_REFUNDED = 'customer_balance_refunded';
	const P__CUSTOMER_BALANCE_TOTAL_REFUNDED = 'customer_balance_total_refunded';
	const P__CUSTOMER_DOB = 'customer_dob';
	const P__CUSTOMER_EMAIL = 'customer_email';
	const P__CUSTOMER_FIRSTNAME = 'customer_firstname';
	const P__CUSTOMER_GENDER = 'customer_gender';
	const P__CUSTOMER_GROUP_ID = 'customer_group_id';
	const P__CUSTOMER_ID = 'customer_id';
	const P__CUSTOMER_IS_GUEST = 'customer_is_guest';
	const P__CUSTOMER_LASTNAME = 'customer_lastname';
	const P__CUSTOMER_MIDDLENAME = 'customer_middlename';
	const P__CUSTOMER_NOTE = 'customer_note';
	const P__CUSTOMER_NOTE_NOTIFY = 'customer_note_notify';
	const P__CUSTOMER_PREFIX = 'customer_prefix';
	const P__CUSTOMER_SUFFIX = 'customer_suffix';
	const P__CUSTOMER_TAXVAT = 'customer_taxvat';
	const P__DISCOUNT_AMOUNT = 'discount_amount';
	const P__DISCOUNT_CANCELED = 'discount_canceled';
	const P__DISCOUNT_DESCRIPTION = 'discount_description';
	const P__DISCOUNT_INVOICED = 'discount_invoiced';
	const P__DISCOUNT_REFUNDED = 'discount_refunded';
	const P__EDIT_INCREMENT = 'edit_increment';
	const P__EMAIL_SENT = 'email_sent';
	const P__ENTITY_ID = 'entity_id';
	const P__EXT_CUSTOMER_ID = 'ext_customer_id';
	const P__EXT_ORDER_ID = 'ext_order_id';
	const P__FORCED_SHIPMENT_WITH_INVOICE = 'forced_shipment_with_invoice';
	const P__GIFT_MESSAGE_ID = 'gift_message_id';
	const P__GLOBAL_CURRENCY_CODE = 'global_currency_code';
	const P__GRAND_TOTAL = 'grand_total';
	const P__HIDDEN_TAX_AMOUNT = 'hidden_tax_amount';
	const P__HIDDEN_TAX_INVOICED = 'hidden_tax_invoiced';
	const P__HIDDEN_TAX_REFUNDED = 'hidden_tax_refunded';
	const P__HOLD_BEFORE_STATE = 'hold_before_state';
	const P__HOLD_BEFORE_STATUS = 'hold_before_status';
	const P__INCREMENT_ID = 'increment_id';
	const P__IS_VIRTUAL = 'is_virtual';
	const P__ORDER_CURRENCY_CODE = 'order_currency_code';
	const P__ORIGINAL_INCREMENT_ID = 'original_increment_id';
	const P__PAYMENT_AUTH_EXPIRATION = 'payment_auth_expiration';
	const P__PAYMENT_AUTHORIZATION_AMOUNT = 'payment_authorization_amount';
	const P__PAYPAL_IPN_CUSTOMER_NOTIFIED = 'paypal_ipn_customer_notified';
	const P__PROTECT_CODE = 'protect_code';
	const P__QUOTE_ADDRESS_ID = 'quote_address_id';
	const P__QUOTE_ID = 'quote_id';
	const P__RELATION_CHILD_ID = 'relation_child_id';
	const P__RELATION_CHILD_REAL_ID = 'relation_child_real_id';
	const P__RELATION_PARENT_ID = 'relation_parent_id';
	const P__RELATION_PARENT_REAL_ID = 'relation_parent_real_id';
	const P__REMOTE_IP = 'remote_ip';
	const P__REWARD_CURRENCY_AMOUNT = 'reward_currency_amount';
	const P__REWARD_CURRENCY_AMOUNT_INVOICED = 'reward_currency_amount_invoiced';
	const P__REWARD_CURRENCY_AMOUNT_REFUNDED = 'reward_currency_amount_refunded';
	const P__REWARD_POINTS_BALANCE = 'reward_points_balance';
	const P__REWARD_POINTS_BALANCE_REFUNDED = 'reward_points_balance_refunded';
	const P__REWARD_POINTS_BALANCE_TO_REFUND = 'reward_points_balance_to_refund';
	const P__REWARD_SALESRULE_POINTS = 'reward_salesrule_points';
	const P__SHIPPING_ADDRESS_ID = 'shipping_address_id';
	const P__SHIPPING_AMOUNT = 'shipping_amount';
	const P__SHIPPING_CANCELED = 'shipping_canceled';
	const P__SHIPPING_DESCRIPTION = 'shipping_description';
	const P__SHIPPING_DISCOUNT_AMOUNT = 'shipping_discount_amount';
	const P__SHIPPING_HIDDEN_TAX_AMOUNT = 'shipping_hidden_tax_amount';
	const P__SHIPPING_INCL_TAX = 'shipping_incl_tax';
	const P__SHIPPING_INVOICED = 'shipping_invoiced';
	const P__SHIPPING_METHOD = 'shipping_method';
	const P__SHIPPING_REFUNDED = 'shipping_refunded';
	const P__SHIPPING_TAX_AMOUNT = 'shipping_tax_amount';
	const P__SHIPPING_TAX_REFUNDED = 'shipping_tax_refunded';
	const P__STATE = 'state';
	const P__STATUS = 'status';
	const P__STORE_CURRENCY_CODE = 'store_currency_code';
	const P__STORE_ID = 'store_id';
	const P__STORE_NAME = 'store_name';
	const P__STORE_TO_BASE_RATE = 'store_to_base_rate';
	const P__STORE_TO_ORDER_RATE = 'store_to_order_rate';
	const P__SUBTOTAL = 'subtotal';
	const P__SUBTOTAL_CANCELED = 'subtotal_canceled';
	const P__SUBTOTAL_INCL_TAX = 'subtotal_incl_tax';
	const P__SUBTOTAL_INVOICED = 'subtotal_invoiced';
	const P__SUBTOTAL_REFUNDED = 'subtotal_refunded';
	const P__TAX_AMOUNT = 'tax_amount';
	const P__TAX_CANCELED = 'tax_canceled';
	const P__TAX_INVOICED = 'tax_invoiced';
	const P__TAX_REFUNDED = 'tax_refunded';
	const P__TOTAL_CANCELED = 'total_canceled';
	const P__TOTAL_DUE = 'total_due';
	const P__TOTAL_INVOICED = 'total_invoiced';
	const P__TOTAL_ITEM_COUNT = 'total_item_count';
	const P__TOTAL_OFFLINE_REFUNDED = 'total_offline_refunded';
	const P__TOTAL_ONLINE_REFUNDED = 'total_online_refunded';
	const P__TOTAL_PAID = 'total_paid';
	const P__TOTAL_QTY_ORDERED = 'total_qty_ordered';
	const P__TOTAL_REFUNDED = 'total_refunded';
	const P__UPDATED_AT = 'updated_at';
	const P__WEIGHT = 'weight';
	const P__X_FORWARDED_FOR ='x_forwarded_for';

	/** @return Df_Sales_Model_Resource_Order_Collection */
	public static function c() {return new Df_Sales_Model_Resource_Order_Collection;}
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Sales_Model_Order
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
	/**
	 * @static
	 * @param int|string $id
	 * @param string|null $field [optional]
	 * @param bool $throwOnError [optional]
	 * @return Df_Sales_Model_Order
	 */
	public static function ld($id, $field = null, $throwOnError = true) {
		return df_load(self::i(), $id, $field, $throwOnError);
	}
	/**
	 * @static
	 * @param string $incrementId
	 * @param bool $throwOnError [optional]
	 * @return Df_Sales_Model_Order
	 */
	public static function ldi($incrementId, $throwOnError = true) {
		return self::ld($incrementId, self::P__INCREMENT_ID, $throwOnError);
	}
	/** @return Df_Sales_Model_Order */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}