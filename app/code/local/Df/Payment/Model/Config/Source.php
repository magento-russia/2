<?php
/**
 * @singleton
 * Система создаёт объект-одиночку для потомков этого класса.
 * Не забывайте об этом при реализации кеширования результатов вычислений внутри этого класса!
 */
abstract class Df_Payment_Model_Config_Source extends Df_Admin_Model_Config_Source {
	/** @return Df_Payment_Model_Method_Base */
	protected function getPaymentMethod() {
		return df_mage()->paymentHelper()->getMethodInstance($this->getPaymentMethodCode());
	}

	/** @return string */
	private function getPaymentMethodCode() {
		return Df_Payment_Model_Method_Base::getCodeByRmId($this->getPaymentMethodRmId());
	}

	/** @return string */
	private function getPaymentMethodRmId() {return df_a($this->getPathExploded(), 1);}

	const _CLASS = __CLASS__;
}