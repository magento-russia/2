<?php
class Df_Ems_Model_Request extends Df_Shipping_Model_Request {
	/**
	 * @param string $paramName
	 * @param mixed $defaultValue[optional]
	 * @return mixed
	 */
	public function getResponseParam($paramName, $defaultValue = null) {
		df_param_string_not_empty($paramName, 0);
		return $this->response()->json(array('rsp', $paramName), $defaultValue);
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
	 * @return Df_Shipping_Model_Request
	 */
	protected function responseFailureDetectInternal() {
		if (self::STATUS__OK !== $this->getResponseParam(self::RESPONSE_PARAM__STATUS)) {
			/** @var string $errorMessage */
			$errorMessage =
				$this->getResponseParam(
					'err/msg'
					,df_mage()->shippingHelper()->__(
						'This shipping method is currently unavailable. '
						.'If you would like to ship using this shipping method, please contact us.'
					)
				)
			;
			/**
			 * EMS сообщает о сбоях на анлийском языке
			 */
			$errorMessage =	df_h()->ems()->__($errorMessage);
			$this->responseFailureHandle($errorMessage);
		}
		return $this;
	}

	/** @return string */
	private function getResponseParam_Status() {
		/** @var string $result */
		$result = $this->getResponseParam(self::RESPONSE_PARAM__STATUS);
		df_result_string($result);
		return $result;
	}

	const _CLASS = __CLASS__;
	const RESPONSE_PARAM__STATUS = 'stat';
	const STATUS__OK = 'ok';
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Ems_Model_Request
	 */
	public static function i(array $parameters = array()) {
		return new self(array(self::P__QUERY_PARAMS => $parameters));
	}
}