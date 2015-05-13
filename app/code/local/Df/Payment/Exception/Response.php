<?php
class Df_Payment_Exception_Response extends Df_Payment_Exception_Client {
	/**
	 * @override
	 * @return string
	 */
	public function getMessageRm() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = implode("\r\n", array(
				$this->getResponse()->getReport()
				,"\r\nАдрес запроса: " . $this->getRequest()->getUri()->getUri()
				,"\r\nПараметры запроса:"
				,rm_print_params($this->getRequest()->getParams())
				,'Ответ сервера:'
				,rm_print_params($this->getResponse()->getData())
			));
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param Df_Payment_Model_Response $response
	 * @return void
	 */
	public function setResponse(Df_Payment_Model_Response $response) {
		$this->setData(self::$P__RESPONSE, $response);
	}

	/** @return Df_Payment_Model_Response */
	protected function getResponse() {return $this->cfg(self::$P__RESPONSE);}

	/** @return Df_Payment_Model_Request_Secondary */
	private function getRequest() {return $this->getResponse()->getRequest();}

	/** @var string */
	private static $P__RESPONSE = 'response';
}