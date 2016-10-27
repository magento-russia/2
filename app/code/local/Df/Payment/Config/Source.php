<?php
namespace Df\Payment\Config;
/**
 * @singleton
 * Система создаёт объект-одиночку для потомков этого класса.
 * Не забывайте об этом при реализации кеширования результатов вычислений внутри этого класса!
 */
abstract class Source extends \Df_Admin_Config_Source {
	/** @return \Df\Payment\Method */
	protected function method() {return
		df_mage()->paymentHelper()->getMethodInstance($this->getPaymentMethodCode())
	;}

	/** @return string */
	private function getPaymentMethodCode() {
		return \Df\Payment\Method::getCodeByRmId($this->getRmId());
	}

	/** @return string */
	private function getRmId() {return dfa($this->getPathExploded(), 1);}
}