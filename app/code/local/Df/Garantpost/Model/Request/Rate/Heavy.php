<?php
class Df_Garantpost_Model_Request_Rate_Heavy extends Df_Garantpost_Model_Request_Rate {
	/** @return int */
	public function getResult() {
		if (!isset($this->{__METHOD__})) {
			/** @var phpQueryObject $pqRate */
			$pqRate = $this->response()->pq('.itog tr:first td')->eq(1);
			$this->{__METHOD__} = rm_preg_match_int('#(\d+)#u', df_trim($pqRate->text()));
		}
		return $this->{__METHOD__};
	}

	/** @return array(string => string|int|float|bool) */
	protected function getPostParameters() {
		return array_merge(parent::getPostParameters(), array(
			'calc_type' => 'cargo'
			// msk — Москва
			// obl — Московская область
			,self::POST_PARAM__LOCATION_ORIGIN_ID => $this->getLocationOriginId()
			// term-term — от терминала до терминала
			// door-term — от двери до терминала
			// term-door — от терминала до двери
			// door-door — от двери до двери
			,self::POST_PARAM__SERVICE => $this->getService()
			,self::POST_PARAM__LOCATION_DESTINATION_NAME =>
				rm_1251_to($this->getLocationDestinationName())
			,self::POST_PARAM__WEIGHT => $this->getWeight()
		));
	}

	/** @return string */
	private function getLocationDestinationName() {
		return $this->cfg(self::P__LOCATION_DESTINATION_NAME);
	}

	/** @return string */
	private function getLocationOriginId() {return $this->cfg(self::P__LOCATION_ORIGIN_ID);}

	/** @return string */
	private function getService() {return $this->cfg(self::P__SERVICE);}

	/** @return string */
	private function getWeight() {return $this->cfg(self::P__WEIGHT);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__LOCATION_DESTINATION_NAME, RM_V_STRING_NE)
			->_prop(self::P__LOCATION_ORIGIN_ID, RM_V_STRING_NE)
			->_prop(self::P__SERVICE, RM_V_STRING_NE)
			->_prop(self::P__WEIGHT, RM_V_FLOAT)
		;
	}
	const _C = __CLASS__;
	const P__LOCATION_DESTINATION_NAME = 'location_destination_name';
	const P__LOCATION_ORIGIN_ID = 'location_origin_id';
	const P__SERVICE = 'service';
	const P__WEIGHT = 'weight';
	const POST_PARAM__LOCATION_DESTINATION_NAME = 'i_to_1';
	const POST_PARAM__LOCATION_ORIGIN_ID = 'i_from_1';
	const POST_PARAM__SERVICE = 'i_service_1';
	const POST_PARAM__WEIGHT = 'i_weight_1';
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Garantpost_Model_Request_Rate_Heavy
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}