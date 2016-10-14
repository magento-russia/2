<?php
class Df_Payment_Exception_Response extends Df_Payment_Exception {
	/**
	 * @param string $message
	 * @param Df_Payment_Model_Response $response
	 */
	public function __construct($message, Df_Payment_Model_Response $response) {
		parent::__construct($message);
		$this->_response = $response;
	}

	/**
	 * @override
	 * @return string
	 */
	public function getMessageRm() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_cc_n(
				$this->getResponse()->getReport()
				,"\nАдрес запроса: " . $this->getRequest()->getUri()->getUri()
				,"\nПараметры запроса:" . df_print_params($this->getRequest()->params())
				,'Ответ сервера:'. df_print_params($this->getResponse()->getData())
			);
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Payment_Model_Response */
	protected function getResponse() {return $this->_response;}

	/** @return Df_Payment_Model_Request_Secondary */
	private function getRequest() {return $this->getResponse()->getRequest();}

	/** @var Df_Payment_Model_Response */
	private $_response;

	/** @used-by Df_Payment_Model_Response::getExceptionClass() */
	const _C = __CLASS__;
}