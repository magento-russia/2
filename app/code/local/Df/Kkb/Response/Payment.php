<?php
namespace Df\Kkb\Response;
use Mage_Sales_Model_Order_Payment_Transaction as T;
class Payment extends \Df\Kkb\Response {
	/** @return string */
	public function getOrderIncrementId() {return dfc($this, function() {
		/** @var string $result */
		$result =
			$this->getErrorMessage()
			? $this->e()->getAttribute('order_id')
			: $this->getElementOrder()->getAttribute('order_id')
		;
		df_result_string_not_empty($result);
		return $result;
	});}

	/** @return float */
	public function getPaymentAmount() {return
		df_float($this->getElementPayment()->getAttribute('amount'))
	;}
	
	/** @return \Df_Core_Model_Money */
	public function getPaymentAmountInServiceCurrency() {return dfc($this, function() {return
		$this->configS()->convertAmountToServiceCurrency(
			$this->getOrderCurrency(), $this->getPaymentAmount()
		)
	;});}

	/** @return string */
	public function getPaymentCodeApproval() {return dfc($this, function() {
		/** @var string $result */
		$result = $this->getElementPayment()->getAttribute('approval_code');
		df_result_string_not_empty($result);
		return $result;
	});}

	/** @return string */
	public function getPaymentId() {return dfc($this, function() {
		/** @var string $result */
		$result = $this->getElementPayment()->getAttribute('reference');
		df_result_string_not_empty($result);
		return $result;
	});}

	/**
	 * @override
	 * @return array(string => string)
	 */
	public function getReportAsArray() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				!$this->isSuccessful()
				? array_filter(array(
					'Диагностическое сообщение' => $this->getErrorMessage()
					,'Дата и время платежа' => df_dts($this->getTime(), 'dd.MM.y HH:mm:ss')
				))
				: df_clean(array(
					'Дата и время платежа' => df_dts($this->getTime(), 'dd.MM.y HH:mm:ss')
					,'Владелец карты' => $this->getCustomerName()
					,'E-mail покупателя' => $this->getCustomerEmail()
					,'Телефон покупателя' => $this->getCustomerPhone()
					,'Размер платежа' => df_f2($this->getPaymentAmount())
					,'Валюта платежа' => $this->getOrderCurrency()->getName()
					,'Была ли проверка 3-D Secure / SecureCode' => df_bts_r($this->isPaymentUsed3DSecure())
					,'Код авторизации' => $this->getPaymentCodeApproval()
				))

			;
		}
		return $this->{__METHOD__};
	}
	
	/**
	 * Тип должен быть именно таким!
	 * Если вернуть Mage_Sales_Model_Order_Payment_Transaction::TYPE_PAYMENT,
	 * то функция разблокировки средств из административного интерфейса не будет доступна.
	 * @see Mage_Sales_Model_Order_Payment::getAuthorizationTransaction()
	 * @override
	 * @return string
	 */
	public function getTransactionType() {return T::TYPE_AUTH;}

	/**
	 * @override
	 * @return bool
	 */
	public function isSuccessful() {
		/** @var bool $result */
		$result = parent::isSuccessful();
		$this->checkPaymentCodeResponse();
		return $result;
	}

	/**
	 * @override
	 * @return bool
	 */
	public function isTransactionClosed() {return false;}

	/**
	 * @override
	 * @return string|null
	 */
	protected function getErrorMessage() {return $this->p()->descendS('response/error');}

	/**
	 * @throws \Exception
	 * @return void
	 */
	private function checkPaymentCodeResponse() {
		if ('00' !== $this->getPaymentCodeResponse()) {
			$this->throwException(
				'Код результата авторизации должен быть «00», однако получено значение «%s».'
				,$this->getPaymentCodeResponse()
			);
		}
	}
	
	/** @return string */
	private function getCustomerEmail() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->getElementCustomer()->getAttribute('mail');
			// Платёжная страница Казкоммерцбанка
			// требует от покупателя обязательного указания адреса электронной почты
			df_result_string_not_empty($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}
	
	/** @return string */
	private function getCustomerName() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->getElementCustomer()->getAttribute('name');
			df_result_string_not_empty($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}
	
	/** @return string */
	private function getCustomerPhone() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_nts($this->getElementCustomer()->getAttribute('phone'));
		}
		return $this->{__METHOD__};
	}
	
	/** @return \Df\Xml\X */
	private function getElementCustomer() {return $this->getElement('bank/customer');}

	/** @return \Df\Xml\X */
	private function getElementOrder() {return $this->getElement('bank/customer/merchant/order');}
	
	/** @return \Df\Xml\X */
	private function getElementPayment() {return $this->getElement('bank/results/payment');}
	
	/** @return \Df_Directory_Model_Currency */
	private function getOrderCurrency() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $currencyCodeInPaymentSystemFormat */
			$currencyCodeInPaymentSystemFormat = $this->getElementOrder()->getAttribute('currency');
			df_assert_string_not_empty($currencyCodeInPaymentSystemFormat);
			$this->{__METHOD__} =
				$this->configS()->getCurrencyByCodeInServiceFormat(
					$currencyCodeInPaymentSystemFormat
				)
			;
		}
		return $this->{__METHOD__};
	}
	
	/** @return string */
	private function getOrderCurrencyCode() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $currencyCodeInPaymentSystemFormat */
			$currencyCodeInPaymentSystemFormat = $this->getElementOrder()->getAttribute('currency');
			df_assert_string_not_empty($currencyCodeInPaymentSystemFormat);
			$this->{__METHOD__} =
				$this->configS()->translateCurrencyCodeReversed(
					$currencyCodeInPaymentSystemFormat
				)
			;
			df_result_string_not_empty($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}
	
	/** @return string */
	private function getPaymentCodeResponse() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->getElementPayment()->getAttribute('response_code');
			df_result_string_not_empty($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}
	
	/** @return \Zend_Date */
	private function getTime() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_date_parse(
				$this->getTimeAsString(), 'y-MM-dd HH:mm:ss', 'Asia/Almaty'
			);
		}
		return $this->{__METHOD__};
	}
	
	/** @return string */
	private function getTimeAsString() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				$this->getErrorMessage()
				? $this->getElement('error')->getAttribute('time')
				: $this->getElement('bank/results')->getAttribute('timestamp')
			;
			df_result_string_not_empty($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}
	
	/** @return bool */
	private function isPaymentUsed3DSecure() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = ('Yes' === $this->getElementPayment()->getAttribute('Secure'));
		}
		return $this->{__METHOD__};
	}


	/**
	 * @static
	 * @param string $xml [optional]
	 * @return self
	 */
	public static function i($xml = null) {return new self(array(self::P__XML => $xml));}
}