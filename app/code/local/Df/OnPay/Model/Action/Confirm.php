<?php
class Df_OnPay_Model_Action_Confirm extends Df_Payment_Model_Action_Confirm {
	/**
	 * @override
	 * @return Df_OnPay_Model_Action_Confirm
	 */
	protected function alternativeProcessWithoutInvoicing() {
		parent::alternativeProcessWithoutInvoicing();
		$this->getOrder()
			->addStatusHistoryComment(
				$this->getPaymentStateMessage(
					$this->getRequestValueServicePaymentState()
				)
			)
		;
		$this->getOrder()->setData(Df_Sales_Const::ORDER_PARAM__IS_CUSTOMER_NOTIFIED, false);
		$this->getOrder()->save();
		return $this;
	}

	/**
	 * @override
	 * @return Df_Payment_Model_Action_Confirm
	 * @throws Mage_Core_Exception
	 */
	protected function checkPaymentAmount() {
		if (
				!$this->needInvoice()
			||
				$this->getServiceConfig()->isFeePayedByBuyer()
		) {
			parent::checkPaymentAmount();
		}
		else {
			/**
			 * Не проверяем, потому что уж слишком хитро всё там с комиссиями.
			 * Нам достаточно проверки при предварительном запросе и поверки подписи.
			 */
		}
		return $this;
	}

	/**
	 * Использовать getConst нельзя из-за рекурсии.
	 * @override
	 * @return string
	 */
	protected function getRequestKeyOrderIncrementId() {
		return 'pay_for';
	}

	/**
	 * @override
	 * @param Exception $e
	 * @return string
	 */
	protected function getResponseTextForError(Exception $e) {
		/** @var array(string => string|int) $responseParams */
		$responseParams =
			array_merge(
				array(
					'code' => 3
					,$this->getRequestKeyOrderIncrementId() => $this->getRequestValueOrderIncrementId()
					,'comment' => df_text()->escapeHtml(rm_ets($e))
					,$this->getRequestKeySignature() => $this->getResponseSignature(3)
				)
				,!$this->needInvoice()
				? array()
				: array(
					$this->getRequestKeyServicePaymentId() => $this->getRequestValueServicePaymentId()
					,'order_id' => $this->getRequestValueOrderIncrementId()
				)
			)
		;
		/** @var string $result */
		$result = $this->responseObjectToXml(new Varien_Object($responseParams));
		df_result_string($result);
		return $result;
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getResponseTextForSuccess() {
		/** @var array(string => string|int) $responseParams */
		$responseParams =
			array_merge(
				array(
					'code' => 0
					,$this->getRequestKeyOrderIncrementId() => $this->getRequestValueOrderIncrementId()
					,'comment' => 'OK'
					,$this->getRequestKeySignature() => $this->getResponseSignature(0)
				)
				,!$this->needInvoice()
				? array()
				: array(
					$this->getRequestKeyServicePaymentId() => $this->getRequestValueServicePaymentId()
					,'order_id' => $this->getRequestValueOrderIncrementId()
				)
			)
		;
		/** @var string $result */
		$result = $this->responseObjectToXml(new Varien_Object($responseParams));
		df_result_string($result);
		return $result;
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getSignatureFromOwnCalculations() {
		/** @var string[] $signatureParams */
		$signatureParams =
			array(
				$this->getRequestValueServicePaymentState()
				,$this->getRequestValueOrderIncrementId()
			)
		;
		if ($this->needInvoice()) {
			$signatureParams[]= $this->getRequestValueServicePaymentId();
		}
		$signatureParams =
			array_merge(
				$signatureParams
				,array(
					$this->getRequestValuePaymentAmountAsString()
					,$this->getRequestValuePaymentCurrencyCode()
					,$this->getResponsePassword()
				)
			)
		;
		/** @var string $result */
		$result = df_h()->onPay()->generateSignature($signatureParams);
		df_result_string($result);
		return $result;
	}

	/**
	 * @override
	 * @return bool
	 */
	protected function needInvoice() {
		return self::PAYMENT_STATE__CHECK !== $this->getRequestValueServicePaymentState();
	}

	/**
	 * @param string $code
	 * @return string
	 */
	private function getPaymentStateMessage($code) {
		df_param_string($code, 0);
		return
			self::PAYMENT_STATE__CHECK === $code
			? 'Платёжная система запрашивает у магазина подтверждение необходимости проведения платежа'
			: ''
		;
	}

	/**
	 * @param int $code
	 * @return string
	 */
	private function getResponseSignature($code) {
		df_param_integer($code, 0);
		/** @var string[] $signatureParams */
		$signatureParams =
			array(
				$this->getRequestValueServicePaymentState()
				,$this->getRequestValueOrderIncrementId()
			)
		;
		if ($this->needInvoice()) {
			$signatureParams =
				array_merge(
					$signatureParams
					,array(
						$this->getRequestValueServicePaymentId()
						,$this->getRequestValueOrderIncrementId()
					)
				)
			;
		}
		$signatureParams =
			array_merge(
				$signatureParams
				,array(
					$this->getRequestValuePaymentAmountAsString()
					,$this->getRequestValuePaymentCurrencyCode()
					,$code
					,$this->getResponsePassword()
				)
			)
		;
		df_assert_array($signatureParams);
		/** @var string $result */
		$result =
			df_h()->onPay()->generateSignature(
				$signatureParams
			)
		;
		df_result_string($result);
		return $result;
	}

	/**
	 * @param Varien_Object $responseAsVarienObject
	 * @return string
	 */
	private function responseObjectToXml(Varien_Object $responseAsVarienObject) {
		/** @var string $result */
		$result =
			$responseAsVarienObject->toXml(
				array()  // все свойства
				, 'result' // корневой тэг
				, true /** добавить <?xml version="1.0" encoding="UTF-8"?>  */
				, false  // Запрещаем добавление CDATA
			)
		;
		df_result_string($result);
		return $result;
	}

	const _CLASS = __CLASS__;
	const PAYMENT_STATE__CHECK = 'check';
	const PAYMENT_STATE__PAID = 'paid';
	/**
	 * @static
	 * @param Df_OnPay_ConfirmController $controller
	 * @return Df_OnPay_Model_Action_Confirm
	 */
	public static function i(Df_OnPay_ConfirmController $controller) {
		return new self(array(self::P__CONTROLLER => $controller));
	}
}