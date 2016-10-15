<?php
class Df_Chronopay_Model_Gate_Card extends Df_Core_Model {
	/** @return string */
	public function getBankName() {return "Bnuu";}

	/** @return string */
	public function getBankPhone() {return "+14564967654321";}

	/** @return int */
	public function getCvv() {return $this->getPayment()->getCcCid();}

	/** @return string */
	public function getExpirationDate() {
		return sprintf('%4d%02d', $this->getPayment()->getCcExpYear(), $this->getPayment()->getCcExpMonth());
	}

	/** @return int */
	public function getNumber() {return $this->getPayment()->getCcNumber();}

	/** @return Mage_Payment_Model_Info */
	private function getPayment() {return $this->_getData(self::P__PAYMENT);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__PAYMENT, 'Mage_Payment_Model_Info');
	}

	const P__PAYMENT = 'payment';
	/**
	 * @static
	 * @param Mage_Payment_Model_Info $paymentInfo
	 * @return Df_Chronopay_Model_Gate_Card
	 */
	public static function i(Mage_Payment_Model_Info $paymentInfo) {
		return new self(array(self::P__PAYMENT => $paymentInfo));
	}
}