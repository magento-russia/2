<?php
class Df_DeliveryUa_Model_Request_Rate extends Df_DeliveryUa_Model_Request {
	/** @return float */
	public function getRateByVolume() {
		return rm_float(df_a($this->getRatesForOriginAndDestination(), self::RATE__BY_VOLUME));
	}

	/** @return float */
	public function getRateByWeight() {
		return rm_float(df_a($this->getRatesForOriginAndDestination(), self::RATE__BY_WEIGHT));
	}

	/**
	 * @override
	 * @return array(string => string|int|float|bool)
	 */
	protected function getPostParameters() {
		return array_merge(parent::getPostParameters(), array(
			'city' => $this->getLocationOriginId()
		));
	}

	/**
	 * @override
	 * @return array(string => int)
	 */
	protected function getQueryParams() {
		return array(
			'id' => 7068
			,'show' => 29952
		);
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getQueryPath() {
		return '/ru/index.php';
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getRequestMethod() {
		return Zend_Http_Client::POST;
	}

	/** @return int */
	private function getLocationDestinationId() {
		return $this->cfg(self::P__LOCATION_DESTINATION_ID);
	}

	/** @return int */
	private function getLocationOriginId() {
		return $this->cfg(self::P__LOCATION_ORIGIN_ID);
	}

	/** @return mixed */
	private function getRatesForOrigin() {
		if (!isset($this->{__METHOD__})) {
			/** @var mixed[] $result */
			/** @var string $cacheKey */
			$cacheKey = $this->getCache()->makeKey(array($this, __FUNCTION__), $this->getLocationOriginId());
			$result = $this->getCache()->loadDataArray($cacheKey);
			if (!is_array($result)) {
				$result = $this->parseRatesForOrigin();
				$this->getCache()->saveDataArray($cacheKey, $result);
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return array(string => string) */
	private function getRatesForOriginAndDestination() {
		/** @var array(string => string) $result */
		$result = df_a($this->getRatesForOrigin(), $this->getLocationDestinationId());
		df_result_array($result);
		return $result;
	}

	/**
	 * @param phpQueryObject $pqTableRow
	 * @return int
	 */
	private function getRow_DestinationCityId(phpQueryObject $pqTableRow) {
		/** @var phpQueryObject $pqTableCellLocation */
		$pqTableCellLocation = $pqTableRow->children()->eq(0);
		/** @var phpQueryObject $pqA */
		$pqA = $pqTableCellLocation->children('a');
		/** @var string $href */
		$href = $pqA->attr('href');
		df_assert_string($href);
		return rm_preg_match_int("#city\=(\d+)#ui", $href);
	}

	/**
	 * @param phpQueryObject $pqTableRow
	 * @return float
	 */
	private function getRow_RateByVolume(phpQueryObject $pqTableRow) {
		/** @var phpQueryObject $pqTableCellRateByVolume */
		$pqTableCellRateByVolume = $pqTableRow->children()->eq(2);
		return rm_float(df_trim($pqTableCellRateByVolume->text()));
	}

	/**
	 * @param phpQueryObject $pqTableRow
	 * @return float
	 */
	private function getRow_RateByWeight(phpQueryObject $pqTableRow) {
		return rm_float(df_trim($pqTableRow->children()->eq(1)->text()));
	}

	/** @return array(int => array(string => float)) */
	private function parseRatesForOrigin() {
		/** @var array(int => array(string => float)) $result */
		$result = array();
		/** @var phpQueryObject $pqTitle */
		$pqTitle = $this->response()->pq('h1.pagetitle');
		/** @var phpQueryObject $pqTable */
		$pqTable = df_pq('table', $pqTitle->parent())->eq(1);
		df_assert_eq(1, count($pqTable));
		/** @var phpQueryObject $pqTableCells */
		$pqTableCells = df_pq('td.tdfn', $pqTable);
		df_assert_eq(1, count($pqTableCells));
		/** @var phpQueryObject $pqTableRows */
		$pqTableRows = $pqTableCells->parent();
		rm_nat(count($pqTableCells));
		foreach ($pqTableRows as $domTableRow) {
			/** @var DOMNode $domTableRow */
			/** @var phpQueryObject $pqTableRow */
			$pqTableRow = df_pq($domTableRow);
			/** @var int $locationId */
			$locationId = $this->getRow_DestinationCityId($pqTableRow);
			$result[$locationId]=
				array(
					self::RATE__BY_VOLUME => $this->getRow_RateByVolume($pqTableRow)
					,self::RATE__BY_WEIGHT => $this->getRow_RateByWeight($pqTableRow)
				)
			;
		}
		return $result;
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__LOCATION_DESTINATION_ID, RM_V_INT)
			->_prop(self::P__LOCATION_ORIGIN_ID, RM_V_INT)
		;
	}
	const _C = __CLASS__;
	const P__LOCATION_DESTINATION_ID = 'location_destination_id';
	const P__LOCATION_ORIGIN_ID = 'location_origin_id';
	const RATE__BY_VOLUME = 'by_volume';
	const RATE__BY_WEIGHT = 'by_weight';
	/**
	 * @static
	 * @param int $originId
	 * @param int $destinationId
	 * @return Df_DeliveryUa_Model_Request_Rate
	 */
	public static function i($originId, $destinationId) {return new self(array(
		self::P__LOCATION_ORIGIN_ID => $originId
		, self::P__LOCATION_DESTINATION_ID => $destinationId
	));}
}