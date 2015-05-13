<?php
class Df_NightExpress_Model_Request_Rate extends Df_NightExpress_Model_Request {
	/** @return float */
	public function getRate() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_float($this->response()->match('#Результат = ([\d,]+) грн#ui'));
			df_assert_gt0($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return array(string => string)
	 */
	protected function getHeaders() {
		return array_merge(parent::getHeaders(),array(
			'Accept' => '*/*'
			,'Cache-Control' => 'no-cache'
			,'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8'
			,'Pragma' => 'no-cache'
			,'X-Requested-With' => 'XMLHttpRequest'
		));
	}

	/**
	 * @override
	 * @return array(string => string|int|float|bool)
	 */
	protected function getPostParameters() {
		return array_merge(parent::getPostParameters(), array(
			'action' => 'calculate'
		));
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getQueryPath() {return '/ajax.php';}

	/**
	 * @override
	 * @return string
	 */
	protected function getRequestMethod() {return Zend_Http_Client::POST;}

	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_NightExpress_Model_Request_Rate
	 */
	public static function i(array $parameters = array()) {
		return new self(array(self::P__POST_PARAMS => $parameters));
	}
}