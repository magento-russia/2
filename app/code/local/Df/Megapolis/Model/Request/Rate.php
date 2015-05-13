<?php
class Df_Megapolis_Model_Request_Rate extends Df_Shipping_Model_Request {
	/** @return float */
	public function getResult() {
		if (!isset($this->{__METHOD__})) {
			$this->responseFailureDetect();
			$this->{__METHOD__} = rm_float($this->response()->json('price'));
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @param string $responseAsText
	 * @return string
	 */
	public function preprocessJson($responseAsText) {return str_replace('=>', ':', $responseAsText);}

	/**
	 * @override
	 * @return Df_Shipping_Model_Request
	 */
	protected function responseFailureDetectInternal() {
		if ($this->response()->contains('{ERR}')) {
			/** @var string $errorMessage */
			$errorMessage = $this->response()->match('#описание ошибки: ([^\n]+)\n#u', false);
			if (is_null($errorMessage)) {
				$errorMessage = self::T__ERROR_MESSAGE__DEFAULT;
			}
			$this->responseFailureHandle($errorMessage);
		}
		return $this;
	}

	/**
	 * @override
	 * @return array(string => string)
	 */
	protected function getHeaders() {
		return array_merge(parent::getHeaders(), array(
			'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8'
			,'Accept-Encoding' => 'gzip, deflate'
			,'Accept-Language' => 'en-us,en;q=0.5'
			,'Connection' => 'keep-alive'
			,'Host' => 'www.megapolis-exp.ru'
			,'Referer' => 'http://www.megapolis-exp.ru/'
			,'User-Agent' => Df_Core_Const::FAKE_USER_AGENT
		));
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getQueryHost() {return 'www.megapolis-exp.ru';}

	/**
	 * @override
	 * @return array(string => int|string|float|bool)
	 */
	protected function getQueryParams() {
		return array_merge(parent::getQueryParams(), array(
			'get_calc' => true
			,'city_id' => $this->getLocationDestination()
			,'type_of_service_id' => 1
			,'weights_name' => $this->getCargoWeightCode()
			,'declared_value' => $this->getCargoDeclaredValue()
		));
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getQueryPath() {return '/api/';}

	/** @return float */
	private function getCargoDeclaredValue() {return $this->cfg(self::P__CARGO__DECLARED_VALUE);}

	/** @return float */
	private function getCargoWeight() {return $this->cfg(self::P__CARGO__WEIGHT);}

	/** @return string */
	private function getCargoWeightCode() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $result */
			$result = null;
			if (0.1 >= $this->getCargoWeight()) {
				$result = '0.0-0.1';
			}
			else if (0.5 >= $this->getCargoWeight()) {
				$result = '0.1-0.5';
			}
			else if (1.0 >= $this->getCargoWeight()) {
				$result = '0.5-1.0';
			}
			else if (1.5 >= $this->getCargoWeight()) {
				$result = '1.0-1.5';
			}
			else if (2.0 >= $this->getCargoWeight()) {
				$result = '1.5-2.0';
			}
			else {
				/** @var int $ceil */
				$ceil = ceil ($this->getCargoWeight());
				df_assert_integer($ceil);
				$result =
					implode(
						'-'
						,array(
							rm_sprintf('%.1f', $ceil - 1)
							,rm_sprintf('%.1f', $ceil)
						)
					)
				;
			}
			df_result_string($result);
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getLocationDestination() {return $this->cfg(self::P__LOCATION__DESTINATION);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__CARGO__DECLARED_VALUE, self::V_FLOAT)
			->_prop(self::P__CARGO__WEIGHT, self::V_FLOAT)
			->_prop(self::P__LOCATION__DESTINATION, self::V_INT)
		;
	}
	const _CLASS = __CLASS__;
	const P__CARGO__DECLARED_VALUE = 'cargo__declared_value';
	const P__CARGO__WEIGHT = 'cargo__weight';
	const P__LOCATION__DESTINATION = 'location__destination';
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Megapolis_Model_Request_Rate
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}