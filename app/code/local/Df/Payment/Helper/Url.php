<?php
class Df_Payment_Helper_Url extends Mage_Payment_Helper_Data {
	/** @return string */
	public function getCheckout() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Mage::getUrl(Df_Checkout_Const::URL__CHECKOUT);
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function getCheckoutFail() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				Mage::getUrl(
					'df-payment/cancel'
					// Без _nosid система будет формировать ссылку вида
					// http://localhost.com:656/df-payment/cancel/?___SID=U,
					// и тогда Единая Касса неверно вычисляет ЭЦП
					,array('_nosid' => true)
				)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function getCheckoutSuccess() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				Mage::getUrl(
					'checkout/onepage/success'
					,/**
					 * Без _nosid система будет формировать ссылку вида
					 * http://localhost.com:656/checkout/onepage/success/?___SID=U,
					 * и тогда Единая Касса неверно вычисляет ЭЦП
					 */
					array('_nosid' => true)
				)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Payment_Helper_Url */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}