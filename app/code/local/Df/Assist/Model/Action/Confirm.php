<?php
class Df_Assist_Model_Action_Confirm extends Df_Payment_Model_Action_Confirm {
	/**
	 * Использовать getConst нельзя из-за рекурсии.
	 * @override
	 * @return string
	 */
	protected function getRequestKeyOrderIncrementId() {return 'ordernumber';}

	/**
	 * @override
	 * @param Exception $e
	 * @return string
	 */
	protected function getResponseTextForError(Exception $e) {
		return df_cc_n(
			df_output()->getXmlHeader()
			,"<pushpaymentresult firstcode='1' secondcode='0'></pushpaymentresult>"
		);
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getResponseTextForSuccess() {
		return df_cc_n(
			df_output()->getXmlHeader()
			,"<pushpaymentresult firstcode='0' secondcode='0'>
				<order>
					<billnumber>{$this->getRequestValueServicePaymentId()}</billnumber>
					<packetdate>{$this->getRequestValueServicePaymentDate()}</packetdate>
				</order>
			</pushpaymentresult>"
		);
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getSignatureFromOwnCalculations() {
		return strtoupper(md5(strtoupper(df_cc(
			md5($this->getResponsePassword())
			,md5(df_cc(
				$this->getRequestValueShopId()
				,$this->getRequestValueOrderIncrementId()
				,$this->getRequestValuePaymentAmountAsString()
				,$this->getRequestValuePaymentCurrencyCode()
				,$this->getRequestValueServicePaymentState()
			))
		))));
	}
}