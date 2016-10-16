<?php
/**
 * @method float|null getBaseCustomerBalanceAmountUsed()
 * @method Df_CustomerBalance_Model_Balance|null getCustomerBalanceInstance()
 * @method Df_Reward_Model_Reward|null getRewardInstance()
 * @method bool|null getUseCustomerBalance()
 * @method bool|null getUseRewardPoints()
 * @method $this setCustomerBalanceCollected(bool $value)
 * @method $this setRewardPointsTotalReseted(bool $value)
 * @method $this setUseCustomerBalance(bool $value)
 * @method $this setUseRewardPoints(bool $value)
 */
class Df_Sales_Model_Quote extends Mage_Sales_Model_Quote {
	/**
	 * 2015-08-08
	 * Цель перекрытия —
	 * кэширование результата работы родительского метода
	 * @see Mage_Sales_Model_Quote::getTotals()
	 * Странно, что родительский метод не кэширует результат своей работы.
	 * Например, на странице корзины (checkout/cart) в Magento CE 1.9.2.1
	 * присутствует 6(!) разных блоков корзины:
		Mage_Checkout_Block_Cart_Minicart
		Mage_Checkout_Block_Cart_Sidebar + render
		Mage_Checkout_Block_Cart
		Mage_Checkout_Block_Cart_Coupon
		Mage_Checkout_Block_Cart_Shipping
		Mage_Checkout_Block_Cart_Totals
	 * Большинство из них вызывает метод @see Mage_Sales_Model_Quote::getTotals(),
	 * и т.к. результат этого метода по непонятным причинам не кэшируется,
	 * получается, что система напрасно расходует ресурсы сервера.
	 *
	 * Конечно, могут быть скрытые причины, почему такое кэширование не осуществляется.
	 * Практика и время покажут.
	 *
	 * @override
	 * @see Mage_Sales_Model_Quote::getTotals()
	 * @return array(string => Mage_Sales_Model_Quote_Address_Total_Abstract)
	 */
	public function getTotals() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = parent::getTotals();
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Sales_Model_Quote
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
	/**
	 * @static
	 * @param int|string $id
	 * @param string|null $field [optional]
	 * @param bool $throwOnError [optional]
	 * @return Df_Sales_Model_Quote
	 */
	public static function ld($id, $field = null, $throwOnError = true) {
		return df_load(self::i(), $id, $field, $throwOnError);
	}
}