<?php
/**
 * @link https://bugs.php.net/bug.php?id=49513
 */
class Df_Zf_Soap_Server extends Zend_Soap_Server {
	/**
	 * @override
	 * @param string|Exception $fault
	 * @param string $code SOAP Fault Codes
	 * @return SoapFault
	 */
	public function fault($fault = null, $code = "Receiver") {
		/** @var Exception $exception */
		$exception = null;
		if (is_string($fault)) {
			$exception = new Df_Core_Exception_Internal ($fault);
		}
		else if ($fault instanceof Exception) {
			$exception = $fault;
		}
		else {
			$exception = new Df_Core_Exception_Internal ('Неизвестный сбой');
		}
		df_assert($exception instanceof Exception);
		df_notify_exception($exception);
		/** @var SoapFault $result */
		$result = parent::fault($fault, $code);
		df_assert($result instanceof SoapFault);
		return $result;
	}

}