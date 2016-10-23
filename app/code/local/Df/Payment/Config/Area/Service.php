<?php
use Df_Sales_Model_Order as O;
class Df_Payment_Config_Area_Service extends Df_Payment_Config_Area {
	/**
	 * @param Mage_Sales_Model_Order|O $order
	 * @param float|string $amountInOrderCurrency
	 * @return Df_Core_Model_Money
	 */
	public function convertAmountFromOrderCurrencyToServiceCurrency(O $order, $amountInOrderCurrency) {
		return $this->convertAmountToServiceCurrency(
			$order->getOrderCurrency(), $amountInOrderCurrency
		);
	}

	/**
	 * @used-by Df_Payment_Request_Transaction::getAmount()
	 * @param Mage_Directory_Model_Currency $currency
	 * @param float|string $amount
	 * @return Df_Core_Model_Money
	 */
	public function convertAmountToServiceCurrency(Mage_Directory_Model_Currency $currency, $amount) {
		/** @var float $amount */
		$amount = (float)$amount;
		return
			df_money(
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
			if (!$this->constManager()->hasCurrencySetRestriction()) {
				$result = $currenciesAllowedInSystem;
			}
			else {
				foreach ($currenciesAllowedInSystem as $option) {
					/** @var array(string => string) $option */
					if (in_array(df_option_v($option), $this->constManager()->allowedCurrencyCodes())) {
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
			/** @var array(string => string) $languages */
			$languages = df_h()->localization()->getLanguages();
			/** @var Df_Localization_Helper_Locale $helper */
			$helper = Df_Localization_Helper_Locale::s();
			foreach ($this->constManager()->allowedLocaleCodes() as $code) {
				$result[]= df_option($code, dfa($languages, $helper->getLanguageCodeByLocaleCode($code)));
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * Способы оплаты, предоставляемые данной платёжной системой
	 * @return array(string => array(string => string))
	 */
	public function availablePaymentMethodsAsOptionArray() {
		return $this->constManager()->availablePaymentMethodsAsOptionArray();
	}

	/** @return string */
	public function getCardPaymentAction() {return $this->getVar('card_payment_action');}

	/** @return Df_Directory_Model_Currency */
	public function getCurrency() {return Df_Directory_Model_Currency::ld($this->getCurrencyCode());}

	/**
	 * Валюта платёжной системы.
	 * Задаётся опцией «payment_service__currency» в файле config.xml платёжного модуля.
	 * @return string
	 */
	public function getCurrencyCode() {return $this->getVar('currency');}

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
			$this->{__METHOD__} = array_diff(
				df_option_values($this->availablePaymentMethodsAsOptionArray())
				,$this->getSelectedPaymentMethods()
			);
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function getFeePayer() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->getVar('fee_payer');
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function getLocaleCode() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->getVar('payment_page_locale');
			df_result_string_not_empty($this->{__METHOD__});
		}
		return $this->{__METHOD__};
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
	 * Если вызов данного метода происходит
	 * при формировании запроса к платёжной системе,
	 * то поле total_due заказа непусто, и используем его.
	 *
	 * Если же вызов данного метода происходит в других ситуациях
	 * (например, при просмотре формы ПД-4), то поле total_due пусто,
	 * и используем поле grand_total.
	 *
	 * Может, всегда использовать grand_total?
	 *
	 * @param O $order
	 * @return Df_Core_Model_Money
	 */
	public function getOrderAmountInServiceCurrency(O $order) {return
		$this->convertAmountFromOrderCurrencyToServiceCurrency(
			$order
			,(double)(!is_null($order->getTotalDue()) ? $order->getTotalDue() : $order->getGrandTotal())
		);
	}

	/**
	 * @param Mage_Sales_Model_Order_Item $orderItem
	 * @return Df_Core_Model_Money
	 */
	public function getOrderItemAmountInServiceCurrency(Mage_Sales_Model_Order_Item $orderItem) {
		return $this->convertAmountFromOrderCurrencyToServiceCurrency(
			$orderItem->getOrder(), (double)$orderItem->getRowTotalInclTax()
		);
	}

	/**
	 * 2015-03-15
	 * Обратите внимание, что многие платёжные системы
	 * используют один и тот же криптографический ключ
	 * как для обращений интернет-магазина к платёжной системе,
	 * так и для обращений платёжной системы к интернет-магазину.
	 * У таких полей в настройках не будет отдельных полей
	 * «request_password» и «response_password»,
	 * а вместо них будет единое поле «password».
	 * Метод @uses _password() сам разберётся, откуда брать значение.
	 * @return string
	 */
	public function getRequestPassword() {return $this->_password(self::$V__REQUEST_PASSWORD);}

	/**
	 * 2015-03-15
	 * Обратите внимание, что многие платёжные системы
	 * используют один и тот же криптографический ключ
	 * как для обращений интернет-магазина к платёжной системе,
	 * так и для обращений платёжной системы к интернет-магазину.
	 * У таких полей в настройках не будет отдельных полей
	 * «request_password» и «response_password»,
	 * а вместо них будет единое поле «password».
	 * Метод @uses _password() сам разберётся, откуда брать значение.
	 * @return string
	 */
	public function getResponsePassword() {return $this->_password(self::$V__RESPONSE_PASSWORD);}

	/** @return string|null */
	public function getSelectedPaymentMethod() {
		if (!isset($this->{__METHOD__})) {
			/** @var string|null $result */
			$result = $this->getVar('payment_method');
			if ('no' === $result) {
				$result = null;
			}
			$this->{__METHOD__} = df_n_set($result);
		}
		return df_n_get($this->{__METHOD__});
	}

	/** @return string|null */
	public function getSelectedPaymentMethodCode() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_n_set(
				dfa(
					dfa(
						$this->constManager()->availablePaymentMethodsAsCanonicalConfigArray()
						,$this->getSelectedPaymentMethod()
						,array()
					)
					,'code'
				)
			);
		}
		return df_n_get($this->{__METHOD__});
	}

	/**
	 * Возвращает значения поля code способа оплаты.
	 * Данный метод имеет смысл, когда значения поля code — числовые
	 * @return string[]
	 */
	public function getSelectedPaymentMethodCodes() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				array_column(
					dfa_select(
						$this->constManager()->availablePaymentMethodsAsCanonicalConfigArray()
						,$this->getSelectedPaymentMethods()
					)
					,'code'
				)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return string[] */
	public function getSelectedPaymentMethods() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $resultAsString */
			$resultAsString = $this->getVar('payment_methods');
			df_assert_string($resultAsString);
			$this->{__METHOD__} =
				Df_Admin_Config_Form_Element_Multiselect::isAll($resultAsString)
				? df_option_values($this->availablePaymentMethodsAsOptionArray())
				: df_csv_parse($resultAsString)
			;
			df_result_array($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param O $order
	 * @return Df_Core_Model_Money
	 */
	public function geShippingAmountInServiceCurrency(O $order) {
		return $this->convertAmountFromOrderCurrencyToServiceCurrency(
			$order, (double)($order->getShippingAmount())
		);
	}

	/** @return string */
	public function getShopId() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->getVar('shop_id');
			df_result_string_not_empty($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/**
	 * @used-by
	 * @return string
	 */
	public function description() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $domain */
			$domain = df_store_domain($this->store());
			$this->{__METHOD__} = strtr($this->getVar('transaction_description', ''), array(
				'{shop.domain}' => $domain
				,'{shop.name}' => $this->store()->getName()
				,'{store.name}' => $this->store()->getGroup()->getName()
				,'{storeView.name}' => $this->store()->getName()
				,'{storeView.code}' => $this->store()->getCode()
				,'{website.domain}' => $domain
				,'{website.name}' => $this->store()->getWebsite()->getName()
				,'{website.code}' => $this->store()->getWebsite()->getCode()
			));
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function getUrlPaymentPage() {return dfc($this, function() {
		/** @var string $result */
		$result = $this->constManager()->getUrl('payment_page', $canBeTest = true);
		df_result_string_not_empty($result);
		return $result;
	});}

	/** @return bool */
	public function isCardPaymentActionAuthorize() {
		return
				Df_Payment_Config_Source_PaymentCard_PaymentAction::VALUE__AUTHORIZE
			===
				$this->getCardPaymentAction()
		;
	}

	/** @return bool */
	public function isCardPaymentActionCapture() {
		return
				Df_Payment_Config_Source_PaymentCard_PaymentAction::VALUE__CAPTURE
			===
				$this->getCardPaymentAction()
		;
	}

	/** @return bool */
	public function isFeePayedByBuyer() {
		return Df_Payment_Config_Source_Service_FeePayer::VALUE__BUYER === $this->getFeePayer();
	}

	/** @return bool */
	public function isFeePayedByShop() {
		return Df_Payment_Config_Source_Service_FeePayer::VALUE__SHOP === $this->getFeePayer();
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
			$resultAsString = $this->getVar('test');
			// Eсли в настройках отсутствует ключ «test»,
			// то модуль будет всегда находиться в рабочем режиме.
			$this->{__METHOD__} = is_null($resultAsString) ? false : df_bool($resultAsString);
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
		return $this->constManager()->translateCurrencyCodeReversed($currencyCodeInPaymentSystemFormat);
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getAreaPrefix() {return 'payment_service';}

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
		return $this->constManager()->translateCurrencyCode($currencyCodeInMagentoFormat);
	}

	/**
	 * Переводит код локали из стандарта Magento в стандарт платёжной системы
	 * @param string $localeCodeInMagentoFormat
	 * @return string
	 */
	private function translateLocaleCode($localeCodeInMagentoFormat) {
		return $this->constManager()->translateLocaleCode($localeCodeInMagentoFormat);
	}

	/**
	 * @used-by getRequestPassword()
	 * @used-by getResponsePassword()
	 * @param string $field
	 * @return string
	 */
	private function _password($field) {
		if (!isset($this->{__METHOD__}[$field])) {
			/** @var string|null $encrypted */
			$encrypted = $this->getVar($field);
			if (!$encrypted) {
				/**
				 * 2015-03-15
				 * Обратите внимание, что многие платёжные системы
				 * используют один и тот же криптографический ключ
				 * как для обращений интернет-магазина к платёжной системе,
				 * так и для обращений платёжной системы к интернет-магазину.
				 * У таких полей в настройках не будет отдельных полей
				 * «request_password» и «response_password»,
				 * а вместо них будет единое поле «password».
				 */
				$encrypted = $this->getVar('password');
			}
			if (!$encrypted) {
				/**
				 * 2015-03-15
				 * Ещё одна тонкость.
				 * До сегодняшнего дня для платёжных систем с единым криптографическим ключом
				 * единое настроечное поле криптографического ключа называлось не «password»,
				 * а «response_password».
				 * Разумеется, такое наименование было неверным, и сегодня я изменил его на «password».
				 * Однако для обратной совместимости мы сейчас дополнительно смотрим
				 * устаревшее поле.
				 */
				$encrypted = $this->getVar(self::$V__RESPONSE_PASSWORD);
			}
			if (!$encrypted) {
				df_error(
					'Администратор магазина должен указать криптографический ключ'
					.' в настройках платёжного модуля.'
				);
			}
			$this->{__METHOD__}[$field] = df_decrypt($encrypted);
		}
		return $this->{__METHOD__}[$field];
	}

	/**
	 * @var string
	 * @used-by getRequestPassword()
	 * @used-by Df_Psbank_Config_Area_Service::getRequestPassword()
	 */
	protected static $V__REQUEST_PASSWORD = 'request_password';

	/**
	 * @var string
	 * @used-by getResponsePassword()
	 * @used-by _password()
	 */
	private static $V__RESPONSE_PASSWORD = 'response_password';
}