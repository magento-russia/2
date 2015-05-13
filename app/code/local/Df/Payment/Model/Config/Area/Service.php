<?php
class Df_Payment_Model_Config_Area_Service extends Df_Payment_Model_Config_Area_Abstract {
	/**
	 * @param Mage_Sales_Model_Order $order
	 * @param float|string $amountInOrderCurrency
	 * @return Df_Core_Model_Money
	 */
	public function convertAmountFromOrderCurrencyToServiceCurrency(
		Mage_Sales_Model_Order $order
		,$amountInOrderCurrency
	) {
		return $this->convertAmountToServiceCurrency(
			$order->getOrderCurrency(), $amountInOrderCurrency
		);
	}

	/**
	 * @param Df_Directory_Model_Currency $currency
	 * @param float|string $amount
	 * @return Df_Core_Model_Money
	 */
	public function convertAmountToServiceCurrency(Df_Directory_Model_Currency $currency, $amount) {
		/** @var float $amount */
		$amount = (float)$amount;
		return
			Df_Core_Model_Money::i(
				$currency->getCode() === $this->getCurrency()->getCode()
				? $amount
				: $currency->convert($amount, $this->getCurrency())
			)
		;
	}

	/** @return array(array(string => string)) */
	public function getAllowedCurrenciesAsOptionArray() {
		if (!isset($this->{__METHOD__})) {
			/** @var array(array(string => string)) $currenciesAllowedInSystem */
			$currenciesAllowedInSystem = Mage::app()->getLocale()->getOptionCurrencies();
			/** @var array(array(string => string)) $result */
			$result = array();
			if (!$this->getConstManager()->hasCurrencySetRestriction()) {
				$result = $currenciesAllowedInSystem;
			}
			else {
				foreach ($currenciesAllowedInSystem as $option) {
					/** @var array(string => string) $option */
					df_assert_array($option);
					/** @var string $code */
					$code = df_a($option, Df_Admin_Model_Config_Source::OPTION_KEY__VALUE);
					df_assert_string($code);
					if (in_array($code, $this->getConstManager()->getAllowedCurrencyCodes())) {
						$result[]= $option;
					}
				}
			}
			df_result_array($result);
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return array(string => array(string => string)) */
	public function getAllowedLocalesAsOptionArray() {
		if (!isset($this->{__METHOD__})) {
			/** @var array(string => array(string => string)) $result */
			$result = array();
			foreach ($this->getConstManager()->getAllowedLocaleCodes() as $code) {
				$result[]=
					array(
						Df_Admin_Model_Config_Source::OPTION_KEY__VALUE => $code
						,Df_Admin_Model_Config_Source::OPTION_KEY__LABEL =>
							df_a(
								df_h()->localization()->getLanguages()
								,df_h()->localization()->locale()
									->getLanguageCodeByLocaleCode($code)
							)
					)
				;
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * Способы оплаты, предоставляемые данной платёжной системой
	 * @return array(string => array(string => string))
	 */
	public function getAvailablePaymentMethodsAsOptionArray() {
		return $this->getConstManager()->getAvailablePaymentMethodsAsOptionArray();
	}

	/** @return string */
	public function getCardPaymentAction() {
		/** @var string $result */
		$result = $this->getVar(self::KEY__VAR__CARD_PAYMENT_ACTION);
		df_result_string($result);
		return $result;
	}

	/** @return Df_Directory_Model_Currency */
	public function getCurrency() {return Df_Directory_Model_Currency::ld($this->getCurrencyCode());}


	/** @return string */
	public function getCurrencyCode() {return $this->getVar(self::KEY__VAR__CURRENCY);}

	/** @return string */
	public function getCurrencyCodeInServiceFormat() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $result */
			$this->{__METHOD__} = $this->translateCurrencyCode($this->getCurrencyCode());
			df_result_string($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/** @return string[] */
	public function getDisabledPaymentMethods() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				array_diff(
					df_column(
						$this->getAvailablePaymentMethodsAsOptionArray()
						,Df_Admin_Model_Config_Source::OPTION_KEY__VALUE
					)
					,$this->getSelectedPaymentMethods()
				)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function getFeePayer() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->getVar(self::KEY__VAR__FEE_PAYER);
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function getLocaleCode() {
		/** @var string $result */
		$result = $this->getVar(self::KEY__VAR__LOCALE);
		df_result_string_not_empty($result);
		return $result;
	}

	/** @return string */
	public function getLocaleCodeInServiceFormat() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $result */
			$this->{__METHOD__} = $this->translateLocaleCode($this->getLocaleCode());
			df_result_string($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param Df_Sales_Model_Order $order
	 * @return Df_Core_Model_Money
	 */
	public function getOrderAmountInServiceCurrency(Df_Sales_Model_Order $order) {
		return
			$this->convertAmountFromOrderCurrencyToServiceCurrency(
				$order
				,(double)(
					/**
					 * Если вызов данного метода происходит
					 * при формировании запроса к платёжной системе,
					 * то поле total_due заказа непусто, и используем его.
					 *
					 * Если же вызов данного метода происходит в других ситуациях
					 * (например, при просмотре формы ПД-4), то поле total_due пусто,
					 * и используем поле grand_total.
					 *
					 * Может, всегда использовать grand_total?
					 */
					!is_null($order->getTotalDue())
					? $order->getTotalDue()
					: $order->getGrandTotal()
				)
			)
		;
	}

	/**
	 * @param Mage_Sales_Model_Order_Item $orderItem
	 * @return Df_Core_Model_Money
	 */
	public function getOrderItemAmountInServiceCurrency(Mage_Sales_Model_Order_Item $orderItem) {
		return
			$this->convertAmountFromOrderCurrencyToServiceCurrency(
				$orderItem->getOrder()
				,(double)($orderItem->getRowTotalInclTax())
			)
		;
	}

	/** @return string */
	public function getRequestPassword() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->decrypt($this->getVar(self::KEY__VAR__REQUEST_PASSWORD));
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function getResponsePassword() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->decrypt($this->getVar(self::KEY__VAR__RESPONSE_PASSWORD));
		}
		return $this->{__METHOD__};
	}

	/** @return string|null */
	public function getSelectedPaymentMethod() {
		if (!isset($this->{__METHOD__})) {
			/** @var string|null $result */
			$result = $this->getVar(self::KEY__VAR__PAYMENT_METHOD);
			if (self::KEY__VAR__PAYMENT_METHOD__NO === $result) {
				$result = null;
			}
			$this->{__METHOD__} = rm_n_set($result);
		}
		return rm_n_get($this->{__METHOD__});
	}

	/** @return string|null */
	public function getSelectedPaymentMethodCode() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_n_set(
				df_a(
					df_a(
						$this->getConstManager()->getAvailablePaymentMethodsAsCanonicalConfigArray()
						,$this->getSelectedPaymentMethod()
						,array()
					)
					,'code'
				)
			);
		}
		return rm_n_get($this->{__METHOD__});
	}

	/**
	 * Возвращает значения поля code способа оплаты.
	 * Данный метод имеет смысл, когда значения поля code — числовые
	 * @return string[]
	 */
	public function getSelectedPaymentMethodCodes() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				df_column(
					array_intersect_key(
						$this->getConstManager()->getAvailablePaymentMethodsAsCanonicalConfigArray()
						,array_flip($this->getSelectedPaymentMethods())
					)
					,'code'
				)
			;
			df_result_array($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/** @return string[] */
	public function getSelectedPaymentMethods() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $resultAsString */
			$resultAsString = $this->getVar(self::KEY__VAR__PAYMENT_METHODS);
			df_assert_string($resultAsString);
			$this->{__METHOD__} =
				(Df_Admin_Model_Config_Form_Element_Multiselect::RM__ALL === $resultAsString)
				? df_column(
					$this->getAvailablePaymentMethodsAsOptionArray()
					,Df_Admin_Model_Config_Source::OPTION_KEY__VALUE
				)
				: df_parse_csv($resultAsString)
			;
			df_result_array($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param Df_Sales_Model_Order $order
	 * @return Df_Core_Model_Money
	 */
	public function geShippingAmountInServiceCurrency(Df_Sales_Model_Order $order) {
		return
			$this->convertAmountFromOrderCurrencyToServiceCurrency(
				$order
				,(double)($order->getShippingAmount())
			)
		;
	}

	/** @return string */
	public function getShopId() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->getVar(self::KEY__VAR__SHOP_ID);
			df_result_string_not_empty($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function getTransactionDescription() {
		return $this->getVar(self::KEY__VAR__TRANSACTION_DESCRIPTION, '');
	}

	/** @return string */
	public function getUrlPaymentPage() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				$this->getConstManager()->getUrl(self::KEY__CONST__URL__PAYMENT_PAGE, $canBeTest = true)
			;
			df_result_string_not_empty($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/** @return bool */
	public function isCardPaymentActionAuthorize() {
		return
				Df_Payment_Model_Config_Source_PaymentCard_PaymentAction::VALUE__AUTHORIZE
			===
				$this->getCardPaymentAction()
		;
	}

	/** @return bool */
	public function isCardPaymentActionCapture() {
		return
				Df_Payment_Model_Config_Source_PaymentCard_PaymentAction::VALUE__CAPTURE
			===
				$this->getCardPaymentAction()
		;
	}

	/** @return bool */
	public function isFeePayedByBuyer() {
		return Df_Payment_Model_Config_Source_Service_FeePayer::VALUE__BUYER === $this->getFeePayer();
	}

	/** @return bool */
	public function isFeePayedByShop() {
		return Df_Payment_Model_Config_Source_Service_FeePayer::VALUE__SHOP === $this->getFeePayer();
	}

	/**
	 * Работает ли модуль в тестовом режиме?
	 * Обратите внимание, что если в настройках отсутствует ключ «test»,
	 * то модуль будет всегда находиться в рабочем режиме.
	 * @return bool
	 */
	public function isTestMode() {
		if (!isset($this->{__METHOD__})) {
			/** @var string|null $resultAsString */
			$resultAsString = $this->getVar(self::KEY__VAR__TEST);
			/**
			 * Eсли в настройках отсутствует ключ «test»,
			 * то модуль будет всегда находиться в рабочем режиме.
			 */
			$this->{__METHOD__} = is_null($resultAsString) ? false : rm_bool($resultAsString);
		}
		return $this->{__METHOD__};
	}

	/**
	 * Переводит код валюты из стандарта платёжной системы в стандарт Magento.
	 * Обратите внимание, что, как правило, конкретный платёжный модуль
	 * использует либо метод translateCurrencyCode, либо метод translateCurrencyCodeReversed,
	 * но не оба вместе!
	 * Например, модуль WebMoney использует метод translateCurrencyCodeReversed,
	 * потому что кодам в формате платёжной системы «U» и «D»
	 * соответствует единый код в формате Magento — «USD» — и использование translateCurrencyCode
	 * просто невозможно в силу неоднозначности перевода «USD» (неясно, переводить в «U» или в «D»).
	 * @param string $currencyCodeInPaymentSystemFormat
	 * @return string
	 */
	public function translateCurrencyCodeReversed($currencyCodeInPaymentSystemFormat) {
		df_param_string($currencyCodeInPaymentSystemFormat, 0);
		return $this->getConstManager()->translateCurrencyCodeReversed($currencyCodeInPaymentSystemFormat);
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getAreaPrefix() {
		return self::AREA_PREFIX;
	}

	/**
	 * Переводит код валюты из стандарта Magento в стандарт платёжной системы.
	 *
	 * Обратите внимание, что конкретный платёжный модуль
	 * использует либо метод translateCurrencyCode, либо метод translateCurrencyCodeReversed,
	 * но никак не оба вместе!
	 *
	 * Например, модуль WebMoney использует метод translateCurrencyCodeReversed,
	 * потому что кодам в формате платёжной системы «U» и «D»
	 * соответствует единый код в формате Magento — «USD» — и использование translateCurrencyCode
	 * просто невозможно в силу необдозначности перевода «USD» (неясно, переводить в «U» или в «D»).
	 *
	 * @param string $currencyCodeInMagentoFormat
	 * @return string
	 */
	protected function translateCurrencyCode($currencyCodeInMagentoFormat) {
		df_param_string($currencyCodeInMagentoFormat, 0);
		/** @var string $result */
		$result =
			$this->getConstManager()->translateCurrencyCode(
				$currencyCodeInMagentoFormat
			)
		;
		df_result_string($result);
		return $result;
	}

	/**
	 * Переводит код локали из стандарта Magento в стандарт платёжной системы
	 * @param string $localeCodeInMagentoFormat
	 * @return string
	 */
	private function translateLocaleCode($localeCodeInMagentoFormat) {
		df_param_string($localeCodeInMagentoFormat, 0);
		/** @var string $result */
		$result =
			$this->getConstManager()->translateLocaleCode(
				$localeCodeInMagentoFormat
			)
		;
		df_result_string($result);
		return $result;
	}
	const _CLASS = __CLASS__;
	const AREA_PREFIX = 'payment_service';
	const KEY__CONST__URL__PAYMENT_PAGE = 'payment_page';
	const KEY__VAR__CARD_PAYMENT_ACTION = 'card_payment_action';
	const KEY__VAR__CURRENCY = 'currency';
	const KEY__VAR__FEE_PAYER = 'fee_payer';
	const KEY__VAR__LOCALE = 'payment_page_locale';
	const KEY__VAR__PAYMENT_METHOD = 'payment_method';
	const KEY__VAR__PAYMENT_METHOD__NO = 'no';
	const KEY__VAR__PAYMENT_METHODS = 'payment_methods';
	const KEY__VAR__REQUEST_PASSWORD = 'request_password';
	const KEY__VAR__RESPONSE_PASSWORD = 'response_password';
	const KEY__VAR__SHOP_ID = 'shop_id';
	const KEY__VAR__TEST = 'test';
	const KEY__VAR__TRANSACTION_DESCRIPTION = 'transaction_description';
}