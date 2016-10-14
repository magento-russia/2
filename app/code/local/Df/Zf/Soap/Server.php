<?php
/** https://bugs.php.net/bug.php?id=49513 */
class Df_Zf_Soap_Server extends Zend_Soap_Server {
	/**
	 * @override
	 * @param string|Exception $fault
	 * @param string $code SOAP Fault Codes
	 * @return SoapFault
	 */
	public function fault($fault = null, $code = 'Receiver') {
		df_notify_exception($fault ? $fault : 'Неизвестный сбой');
		return parent::fault($fault, $code);
	}
}