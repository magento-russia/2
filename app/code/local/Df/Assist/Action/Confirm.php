<?php
class Df_Assist_Action_Confirm extends Df_Payment_Action_Confirm {
	/**
	 * Использовать getConst нельзя из-за рекурсии.
	 * @override
	 * @return string
	 */
	protected function rkOII() {return 'ordernumber';}

	/**
	 * @override
	 * @param Exception $e
	 * @return string
	 */
	protected function responseTextForError(Exception $e) {
		return df_cc_n(
			df_output()->getXmlHeader()
			,"<pushpaymentresult firstcode='1' secondcode='0'></pushpaymentresult>"
		);
	}

	/**
	 * @override
	 * @return string
	 */
	protected function responseTextForSuccess() {
		return df_cc_n(
			df_output()->getXmlHeader()
			,"<pushpaymentresult firstcode='0' secondcode='0'>
				<order>
					<billnumber>{$this->rExternalId()}</billnumber>
					<packetdate>{$this->rTime()}</packetdate>
				</order>
			</pushpaymentresult>"
		);
	}

	/**
	 * @override
	 * @return string
	 */
	protected function signatureOwn() {
		return strtoupper(md5(strtoupper(df_c(
			md5($this->getResponsePassword())
			,md5(df_c(
				$this->rShopId()
				,$this->rOII()
				,$this->rAmountS()
				,$this->rCurrencyC()
				,$this->rState()
			))
		))));
	}
}