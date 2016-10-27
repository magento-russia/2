<?php
namespace Df\Payment\Exception;
use Df\Payment\Response as R;
class Response extends \Df\Payment\Exception {
	/**
	 * @param string $message
	 * @param R $response
	 */
	public function __construct($message, R $response) {
		parent::__construct($message);
		$this->_response = $response;
	}

	/**
	 * @override
	 * @return string
	 */
	public function message() {
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

	/** @return R */
	protected function getResponse() {return $this->_response;}

	/** @return \Df\Payment\Request\Secondary */
	private function getRequest() {return $this->getResponse()->getRequest();}

	/** @var R */
	private $_response;

	/** @used-by \Df\Payment\Response::getExceptionClass() */

}