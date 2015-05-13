<?php
class Df_InTime_Model_Request_Rate extends Df_InTime_Model_Request {
	/** @return array(string => mixed[]) */
	public function getResultTable() {
		if (!isset($this->{__METHOD__})) {
			/** @var array(string => mixed[]) $result */
			/** @var string $cacheKey */
			$cacheKey =
				$this->getCache()->makeKey(
					array($this, __FUNCTION__)
					,array($this->getLocationOriginId(), $this->getLocationDestinationId())
				)
			;
			$result = $this->getCache()->loadDataArray($cacheKey);
			if (!is_array($result)) {
				$result = $this->response()->json();
				$this->getCache()->saveDataArray($cacheKey, $result);
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return array(string => string|int|float|bool)
	 */
	protected function getPostParameters() {
		return array_merge(parent::getPostParameters(), array(
			'tid_from' => $this->getLocationOriginId()
			,'tid_to' => $this->getLocationDestinationId()
			,'dataType' => 'json'
		));
	}

	/** @return int */
	private function getLocationDestinationId() {
		return $this->cfg(self::P__LOCATION_DESTINATION_ID);
	}

	/** @return int */
	private function getLocationOriginId() {return $this->cfg(self::P__LOCATION_ORIGIN_ID);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__LOCATION_DESTINATION_ID, self::V_INT)
			->_prop(self::P__LOCATION_ORIGIN_ID, self::V_INT)
		;
	}
	const _CLASS = __CLASS__;
	const P__LOCATION_DESTINATION_ID = 'location_destination_id';
	const P__LOCATION_ORIGIN_ID = 'location_origin_id';
	/**
	 * @static
	 * @param int $locationOriginId
	 * @param int $locationDestinationId
	 * @return Df_InTime_Model_Request_Rate
	 */
	public static function i($locationOriginId, $locationDestinationId) {return new self(array(
		self::P__LOCATION_ORIGIN_ID => $locationOriginId
		, self::P__LOCATION_DESTINATION_ID => $locationDestinationId
	));}
}