<?php
class Df_Ems_Request extends Df_Shipping_Request {
	/**
	 * @param string $paramName
	 * @param mixed $default [optional]
	 * @return mixed
	 */
	public function getResponseParam($paramName, $default = null) {
		df_param_string_not_empty($paramName, 0);
		return $this->response()->json(array('rsp', $paramName), $default);
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getQueryHost() {return 'emspost.ru';}

	/**
	 * @override
	 * @return string
	 */
	protected function getQueryPath() {return '/api/rest';}

	/**
	 * @override
	 * @return void
	 * @throws Exception
	 */
	protected function responseFailureDetect() {
		if ('ok' !== $this->getResponseParam('stat')) {
			/** @var string $errorMessage */
			// EMS сообщает о сбоях на английском языке
			df_error(Df_Ems_Helper_Data::s()->__(
				$this->getResponseParam('err/msg', df_mage()->shippingHelper()->__(
					'This shipping method is currently unavailable. '
					.'If you would like to ship using this shipping method, please contact us.'
				))
			));
		}
	}

	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Ems_Request
	 */
	public static function i(array $parameters = array()) {
		return new self(array(self::P__QUERY_PARAMS => $parameters));
	}
}