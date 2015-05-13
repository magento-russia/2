<?php
class Df_Ems_Model_Api_GetConditions extends Df_Ems_Model_Request {
	/** @return float */
	public function getRate() {
		$this->responseFailureDetect();
		return
			self::NO_INTERNET
			? 520.0
			: rm_float($this->getResponseParam(self::RESPONSE_PARAM__PRICE))
		;
	}

	/** @return int */
	public function getTimeOfDeliveryMax() {
		$this->responseFailureDetect();
		/** @var int $result */
		$result =
			self::NO_INTERNET
			? 6
			: $this->getResponseParam(self::RESPONSE_PARAM__TIME_OF_DELIVERY__MAX)
		;
		/**
		 * Для международных отправлений калькулятор EMS не сообщает сроки
		 */
		if (is_null($result)) {
			$result = 0;
		}
		df_result_integer($result);
		return $result;
	}

	/** @return int */
	public function getTimeOfDeliveryMin() {
		$this->responseFailureDetect();
		/** @var int $result */
		$result =
			self::NO_INTERNET
			? 3
			: $this->getResponseParam(self::RESPONSE_PARAM__TIME_OF_DELIVERY__MIN)
		;
		/**
		 * Для международных отправлений калькулятор EMS не сообщает сроки
		 */
		if (is_null($result)) {
			$result = 0;
		}
		df_result_integer($result);
		return $result;
	}

	/**
	 * @override
	 * @return array(string => string|int|float|bool)
	 */
	protected function getQueryParams() {
		return array(
			'method' => 'ems.calculate'
			,'from' => $this->getSource()
			,'to' => $this->getDestination()
			,'weight' => $this->getWeight()
			,'type' => $this->getPostingType()
		);
	}

	/** @return string */
	private function getDestination() {return $this->cfg(self::P__DESTINATION);}

	/** @return string|null */
	private function getPostingType() {return $this->cfg(self::P__POSTING_TYPE);}

	/** @return string */
	private function getSource() {return $this->cfg(self::P__SOURCE);}

	/** @return float */
	private function getWeight() {return $this->cfg(self::P__WEIGHT);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__DESTINATION, self::V_STRING_NE)
			->_prop(self::P__POSTING_TYPE, self::V_STRING, false)
			->_prop(self::P__SOURCE, self::V_STRING_NE)
			->_prop(self::P__WEIGHT, self::V_FLOAT)
		;
	}
	const _CLASS = __CLASS__;
	const P__DESTINATION = 'destination';
	const P__POSTING_TYPE = 'posting_type';
	const P__SOURCE = 'source';
	const P__WEIGHT = 'weight';
	const RESPONSE_PARAM__PRICE = 'price';
	const RESPONSE_PARAM__TIME_OF_DELIVERY__MAX = 'term/max';
	const RESPONSE_PARAM__TIME_OF_DELIVERY__MIN = 'term/min';
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Ems_Model_Api_GetConditions
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}