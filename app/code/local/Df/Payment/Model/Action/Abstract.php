<?php
abstract class Df_Payment_Model_Action_Abstract extends Df_Core_Model_Controller_Action {
	/**
	 * @abstract
	 * @return Df_Sales_Model_Order
	 */
	abstract protected function getOrder();

	/**
	 * @param string $configKey
	 * @param bool $isRequired[optional]
	 * @param string $defaultValue[optional]
	 * @return string
	 */
	protected function getConst(
		$configKey
		,$isRequired = true
		,$defaultValue = ''
	) {
		df_param_string($configKey, 0);
		df_param_boolean($isRequired, 1);
		df_param_string($defaultValue, 2);
		/** @var string $key */
		$key = rm_config_key(self::CONFIG_BASE, $configKey);
		/** @var string $result */
		$result = $this->getPaymentMethod()->getConst($key, $canBeTest = false);
		if ('' === $result) {
			if ($isRequired) {
				df_error(
					self::T__REQUIRED_KEY_IS_ABSENT
					,df()->reflection()->getModuleName(
						get_class(
							/**
							 * Раньше тут стояло $this,
							 * но $this->getPaymentMethod() вроде точнее
							 */
							$this->getPaymentMethod()
						)
					)
					,$key
				);
			}
			$result = $defaultValue;
		}
		df_result_string($result);
		return $result;
	}

	/** @return Df_Payment_Model_Method_WithRedirect */
	protected function getPaymentMethod() {
		if (!isset($this->{__METHOD__})) {
			/** @var Mage_Sales_Model_Order_Payment $payment */
			$payment = $this->getOrder()->getPayment();
			/**
			 * Не используем тут df_assert, потому что эта функция может быть отключена,
			 * а нам важно показать правильное диагностическое сообщение,
			 * а не «Call to a member function getMethodInstance() on a non-object»
			 */
			if (!($payment instanceof Mage_Sales_Model_Order_Payment)) {
				df_error(
					'Платёжная система прислала сообщение
					относительно заказа №«%s», который не предназначен для оплаты.'
					,$this->getOrder()->getIncrementId()
				);
			}
			/** @var Df_Payment_Model_Method_WithRedirect $result */
			$result = $payment->getMethodInstance();
			if (!$result instanceof Df_Payment_Model_Method_WithRedirect) {
				df_error(
					'Платёжная система прислала сообщение'
					.' относительно заказа №«%s»,'
					. ' который не предназначен для оплаты порледством данной платёжной системы.'
					,$this->getOrder()->getIncrementId()
				);
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	const _CLASS = __CLASS__;
	const CONFIG_BASE = 'request/confirmation';
	const T__REQUIRED_KEY_IS_ABSENT = 'В файле «config.xml» модуля «%s» отсутствует требуемый ключ «%s».';
}