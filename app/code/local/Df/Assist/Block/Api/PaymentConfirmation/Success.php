<?php
class Df_Assist_Block_Api_PaymentConfirmation_Success extends Df_Core_Block_Template_NoCache {
	/** @return string */
	public function getBillNumber() {return $this->cfg(self::P__BILL_NUMBER);}

	/** @return string */
	public function getPacketDate() {return $this->cfg(self::P__PACKET_DATE);}

	/**
	 * @override
	 * @return string
	 */
	public function getTemplate() {return self::RM__TEMPLATE;}

	const P__BILL_NUMBER = 'bill_number';
	const P__PACKET_DATE = 'packet_date';
	const RM__TEMPLATE = 'df/assist/api/payment-confirmation/success.xml';
	/**
	 * @param string $paymentId
	 * @param string $paymentDate
	 * @return Df_Assist_Block_Api_PaymentConfirmation_Success
	 */
	public static function i($paymentId, $paymentDate) {
		return df_block(new self(array(
			self::P__BILL_NUMBER => $paymentId, self::P__PACKET_DATE => $paymentDate
		)));
	}
}