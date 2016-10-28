<?php
namespace Df\WebPay\Request;
use Df_Core_Model_Money as Money;
/**
 * @method \Df\WebPay\Config\Area\Service configS()
 * @method \Df\WebPay\Method method()
 */
class Payment extends \Df\Payment\Request\Payment {
	/**
	 * @override
	 * @see \Df\Payment\Request\Payment::_params()
	 * @used-by \Df\Payment\Request\Payment::params()
	 * @return array(string => string|int)
	 */
	protected function _params() {
		/**
		 * Документация WEBPAY:
		 *
		 * «Оплата не будет произведена,
		 * если wsb_total и посчитанное значения товаров не будут совпадать.
		 * Покупателю будет отображена ошибка.»
		 *
		 * Выявляем эту проблему на максимально ранней стадии.
		 */
		$this->verifyAmount();
		/** @var array(string => string) $result */
		$result = array(
			'*scart' => ''
			,self::REQUEST_VAR__SHOP_ID => $this->shopId()
			,self::REQUEST_VAR__SHOP_NAME => $this->description()
			,self::REQUEST_VAR__ORDER_NUMBER => $this->orderIId()
			,self::REQUEST_VAR__ORDER_CURRENCY =>
				$this->configS()->getCurrencyCodeInServiceFormat()
			,'wsb_version' => '2'
			,self::REQUEST_VAR__PAYMENT_SERVICE__LANGUAGE => $this->localeCode()
			,self::REQUEST_VAR__OPEN_KEY => $this->getOpenKey()
			,self::REQUEST_VAR__SIGNATURE => $this->getSignature()
			,self::REQUEST_VAR__URL_RETURN_OK => df_url_checkout_success()
			,self::REQUEST_VAR__URL_RETURN_NO => df_url_checkout_fail()
			,self::REQUEST_VAR__URL_CONFIRM => $this->urlConfirm()
			,self::REQUEST_VAR__REQUEST__TEST_MODE => $this->isTestMode()
			,'wsb_invoice_item_name' => $this->getOrderItemNames()
			,'wsb_invoice_item_quantity' => $this->getOrderItemQtys()
			,'wsb_invoice_item_price' => df_each($this->getOrderItemPrices(), 'getAsInteger')
			,'wsb_tax' => $this->getAmountTaxCorrected()->getAsInteger()
			,'wsb_shipping_name' => $this->order()->getShippingCarrier()->getConfigData('name')
			,'wsb_shipping_price' => $this->getAmountShipping()->getAsInteger()
			,'wsb_discount_name' => 'Скидка'
			,'wsb_discount_price' => $this->getAmountDiscountCorrected()->getAsInteger()
			// WEBPAY требует, чтобы суммы были целыми числами
			,self::REQUEST_VAR__ORDER_AMOUNT => $this->amount()->getAsInteger()
			,self::REQUEST_VAR__CUSTOMER__EMAIL => $this->email()
			,self::REQUEST_VAR__CUSTOMER__PHONE => $this->phone()
		);
		return $result;
	}

	/**
	 * Этот метод может быть приватным,
	 * несмотря на использование его как callable,
	 * потому что он используется как callable только внутри своего класса:
	 * @used-by getOrderItemPrices()
	 * http://php.net/manual/language.types.callable.php#113447
	 * Проверял, что это действительно допустимо, на различных версиях интерпретатора PHP:
	 * http://3v4l.org/OipEQ
	 * @param float|string $amountInOrderCurrency
	 * @return Money
	 */
	private function convertAmountFromOrderCurrencyToServiceCurrency($amountInOrderCurrency) {
		return $this->configS()->convertAmountFromOrderCurrencyToServiceCurrency(
			$this->order(), $amountInOrderCurrency
		);
	}

	/** @return Money */
	private function getAmountDiscount() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				$this->convertAmountFromOrderCurrencyToServiceCurrency(
						$this->order()->getDiscountAmount()
					/**
					 * Обратите внимание, что помимо стандартных скидок Magento Community Edition
					 * мы должны учесть скидки накопительной программы и личного счёта.
					 *
					 * Модули "Накопительная программа" и "Личный счёт"
					 * не добавляют свои скидки к общей скидке.
					 * Поэтому нам надо учесть их скидки вручную
					 */
					+
						$this->order()->getData('reward_currency_amount')
					+
						$this->order()->getData('customer_balance_amount')
				)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return Money */
	private function getAmountDiscountCorrected() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_money(
					$this->getAmountDiscount()->getAsInteger()
				-
					/**
					 * delta < 0 означает, что рассчитанная Magento заказа
					 * ниже, чем рассчитанная вручную.
					 * Чтобы уравнять обе суммы,
					 * увеличиваем скидку для суммы, рассчитанной вручную.
					 * Мы применяем операцию вычитания, потому что
					 * второй операнд гарантированно неположителен.
					 */
					min(0, $this->getAmountDelta()->getAsInteger())
			);
		}
		return $this->{__METHOD__};
	}

	/** @return Money */
	private function getAmountShipping() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->convertAmountFromOrderCurrencyToServiceCurrency(
				$this->order()->getShippingAmount()
			);
		}
		return $this->{__METHOD__};
	}

	/** @return Money */
	private function getAmountTax() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->convertAmountFromOrderCurrencyToServiceCurrency(
				$this->order()->getTaxAmount()
			);
		}
		return $this->{__METHOD__};
	}

	/** @return Money */
	private function getAmountTaxCorrected() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_money(
					$this->getAmountTax()->getAsInteger()
				+
					/**
					 * delta > 0 означает, что рассчитанная Magento заказа
					 * выше, чем рассчитанная вручную.
					 * Чтобы уравнять обе суммы,
					 * увеличиваем налог для суммы, рассчитанной вручную.
					 */
					max(0, $this->getAmountDelta()->getAsInteger())
			);
		}
		return $this->{__METHOD__};
	}

	/** @return Money */
	private function getCalculatedAmountSubtotal() {
		if (!isset($this->{__METHOD__})) {
			/** @var int $resultAsInteger */
			$resultAsInteger = 0;
			/** @var array $tuple */
			$tuple = df_tuple(array(
				'price' => $this->getOrderItemPrices(), 'qty' => $this->getOrderItemQtys()
			));
			foreach ($tuple as $item) {
				/** @var array $item */
				df_assert_array($item);
				/** @var Money $price */
				$price = dfa($item, 'price');
				df_assert($price instanceof Money);
				/** @var int $qty */
				$qty = dfa($item, 'qty');
				df_assert_integer($qty);
				$resultAsInteger += ($qty * $price->getAsInteger());
			}
			$this->{__METHOD__} = df_money($resultAsInteger);
		}
		return $this->{__METHOD__};
	}

	/** @return Money */
	private function getCalculatedAmountTotal() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_money(
					$this->getCalculatedAmountSubtotal()->getAsInteger()
				+
					$this->getAmountShipping()->getAsInteger()
				+
					$this->getAmountTax()->getAsInteger()
				-
					$this->getAmountDiscount()->getAsInteger()
			);
		}
		return $this->{__METHOD__};
	}

	/** @return Money */
	private function getCalculatedAmountTotalCorrected() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_money(
					$this->getCalculatedAmountSubtotal()->getAsInteger()
				+
					$this->getAmountShipping()->getAsInteger()
				+
					$this->getAmountTaxCorrected()->getAsInteger()
				-
					$this->getAmountDiscountCorrected()->getAsInteger()
			);
		}
		return $this->{__METHOD__};
	}

	/** @return Money */
	private function getAmountDelta() {
		if (!isset($this->{__METHOD__})) {
			/** @var Money $result */
			$result = df_money(
				$this->amount()->getAsInteger() - $this->getCalculatedAmountTotal()->getAsInteger()
			);
			if (0 !== $result->getAsInteger()) {
				df_notify(
					"Рассчитанная вручную стоимость заказа не соответствует рассчитанной Magento."
					."\nCтоимость, рассчитанная вручную: %s."
					."\nCтоимость, рассчитанная Magento: %s."
					."\nМодуль «WEBPAY» уравняет эти суммы посредством изменения налога или скидки."
					,$this->getCalculatedAmountTotal()->getAsInteger()
					,$this->amount()->getAsInteger()
				);
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getOpenKey() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_uid();
		}
		return $this->{__METHOD__};
	}

	/** @return \Mage_Sales_Model_Resource_Order_Item_Collection */
	private function getOrderItems() {return $this->order()->getItemsCollection();}

	/** @return string[] */
	private function getOrderItemNames() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->getOrderItems()->getColumnValues('name');
		}
		return $this->{__METHOD__};
	}

	/** @return Money[] */
	private function getOrderItemPrices() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				array_map(
					/** @uses convertAmountFromOrderCurrencyToServiceCurrency() */
					array($this, 'convertAmountFromOrderCurrencyToServiceCurrency')
					,$this->getOrderItems()->getColumnValues('price')
				)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return int[] */
	private function getOrderItemQtys() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_int($this->getOrderItems()->getColumnValues('qty_ordered'));
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getSignature() {
		return sha1(df_c($this->preprocessParams(array(
			self::REQUEST_VAR__OPEN_KEY => $this->getOpenKey()
			,self::REQUEST_VAR__SHOP_ID => $this->shopId()
			,self::REQUEST_VAR__ORDER_NUMBER => $this->orderIId()
			,self::REQUEST_VAR__REQUEST__TEST_MODE => $this->isTestMode()
			,self::REQUEST_VAR__ORDER_CURRENCY =>
				$this->configS()->getCurrencyCodeInServiceFormat()
			,self::REQUEST_VAR__ORDER_AMOUNT => $this->amount()->getAsInteger()
			,self::SIGNATURE_PARAM__ENCRYPTION_KEY => $this->password()
		))));
	}

	/**
	 * Документация WEBPAY:
	 * «Оплата не будет произведена,
	 * если wsb_total и посчитанное значения товаров не будут совпадать.
	 * Покупателю будет отображена ошибка.»
	 * Выявляем эту проблему на максимально ранней стадии.
	 * @return void
	 */
	private function verifyAmount() {
		/** @var Money $calculatedAmountTotalCorrected */
		$calculatedAmountTotalCorrected = df_money(
				$this->getCalculatedAmountSubtotal()->getAsInteger()
			+
				$this->getAmountShipping()->getAsInteger()
			+
				$this->getAmountTaxCorrected()->getAsInteger()
			-
				$this->getAmountDiscountCorrected()->getAsInteger()
		);
		/** @var Money $deltaCorrected */
		$deltaCorrected = df_money(
			$this->amount()->getAsInteger() - $this->getCalculatedAmountTotalCorrected()->getAsInteger()
		);
		if (0 !== $deltaCorrected->getAsInteger()) {
			df_notify(
				"Рассчитанная вручную стоимость заказа"
				. " даже после корректировки не соответствует рассчитанной Magento."
				. "\nЭто — ошибка программиста."
				. "\nСтоимость, рассчитанная вручную: %s."
				. "\nСтоимость, рассчитанная Magento: %s."
				. "\nМодуль «WEBPAY» уравняет эти суммы посредством изменения налога или скидки."
				,$calculatedAmountTotalCorrected->getAsInteger()
				,$this->amount()->getAsInteger()
			);
		}
	}

	/** @return int */
	private function isTestMode() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_01(
				$this->configS()->isTestMode() || $this->configS()->isTestModeOnProduction()
			);
		}
		return $this->{__METHOD__};
	}

	const PAYMENT_MODE__FIX = 'fix';
	const REQUEST_VAR__CUSTOMER__EMAIL = 'wsb_email';
	const REQUEST_VAR__CUSTOMER__PHONE = 'wsb_phone';
	const REQUEST_VAR__OPEN_KEY = 'wsb_seed';
	const REQUEST_VAR__ORDER_COMMENT = 'wsb_store';
	const REQUEST_VAR__ORDER_NUMBER = 'wsb_order_num';
	const REQUEST_VAR__ORDER_CURRENCY = 'wsb_currency_id';
	const REQUEST_VAR__PAYMENT_SERVICE__LANGUAGE = 'wsb_language_id';
	const REQUEST_VAR__PAYMENT_SERVICE__IS_FEE_PAYED_BY_SHOP = 'price_final';
	const REQUEST_VAR__PAYMENT_SERVICE__NEED_CONVERT_RECEIPTS = 'convert';
	const REQUEST_VAR__PAYMENT_SERVICE__PAYMENT_MODE = 'pay_mode';
	const REQUEST_VAR__REQUEST__TEST_MODE = 'wsb_test';
	const REQUEST_VAR__SIGNATURE = 'wsb_signature';
	const REQUEST_VAR__SHOP_ID = 'wsb_storeid';
	const REQUEST_VAR__SHOP_NAME = 'wsb_store';
	const REQUEST_VAR__URL_RETURN_OK = 'wsb_return_url';
	const REQUEST_VAR__URL_RETURN_NO = 'wsb_cancel_return_url';
	const REQUEST_VAR__URL_CONFIRM = 'wsb_notify_url';
	const REQUEST_VAR__ORDER_AMOUNT = 'wsb_total';
	const SIGNATURE_PARAM__ENCRYPTION_KEY = 'encryption_key';
}