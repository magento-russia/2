<?php
/**
 * @singleton
 * Система создаёт объект-одиночку для потомков этого класса.
 * Не забывайте об этом при реализации кеширования результатов вычислений внутри этого класса!
 */
abstract class Df_Payment_Config_Source extends Df_Admin_Config_Source {
	/** @return Df_Payment_Method */
	protected function method() {return
		df_mage()->paymentHelper()->getMethodInstance($this->getPaymentMethodCode())
	;}

	/** @return string */
	private function getPaymentMethodCode() {
		return Df_Payment_Method::getCodeByRmId($this->getRmId());
	}

	/** @return string */
	private function getRmId() {return dfa($this->getPathExploded(), 1);}
}