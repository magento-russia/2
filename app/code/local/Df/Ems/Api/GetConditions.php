<?php
class Df_Ems_Api_GetConditions extends Df_Ems_Request {
	/**
	 * @override
	 * @return float
	 */
	protected function _getRate() {return $this->getResponseParam('price');}

	/**
	 * Для международных отправлений калькулятор EMS не сообщает сроки
	 * @override
	 * @param string|int|null $value
	 * @return int
	 */
	protected function _filterDeliveryTime($value) {return is_null($value) ? 0 : df_nat($value);}

	/**
	 * @override
	 * @return int
	 */
	protected function _getDeliveryTimeMax() {return $this->getResponseParam('term/max');}

	/**
	 * @override
	 * @return int
	 */
	protected function _getDeliveryTimeMin() {return $this->getResponseParam('term/min');}

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
	private function getDestination() {return $this->cfg(self::$P__DESTINATION);}

	/** @return string */
	private function getPostingType() {return $this->cfg(self::$P__POSTING_TYPE);}

	/** @return string */
	private function getSource() {return $this->cfg(self::$P__SOURCE);}

	/** @return float */
	private function getWeight() {return $this->cfg(self::$P__WEIGHT);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::$P__DESTINATION, DF_V_STRING_NE)
			->_prop(self::$P__POSTING_TYPE, DF_V_STRING)
			->_prop(self::$P__SOURCE, DF_V_STRING_NE)
			->_prop(self::$P__WEIGHT, DF_V_FLOAT)
		;
	}
	/** @var string */
	private static $P__DESTINATION = 'destination';
	/** @var string */
	private static $P__POSTING_TYPE = 'posting_type';
	/** @var string */
	private static $P__SOURCE = 'source';
	/** @var string */
	private static $P__WEIGHT = 'weight';
	/**
	 * @static
	 * @param string $source
	 * @param string $destination
	 * @param float $weight
	 * @param string $postingType
	 * @return Df_Ems_Api_GetConditions
	 */
	public static function i($source, $destination, $weight, $postingType) {
		return new self(array(
			self::$P__SOURCE => $source
			, self::$P__DESTINATION => $destination
			, self::$P__WEIGHT => $weight
			, self::$P__POSTING_TYPE => $postingType
		));
	}
}