<?php
class Df_Garantpost_Model_Request_Rate_Light extends Df_Garantpost_Model_Request_Rate {
	/** @return float */
	public function getResult() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_float($this->response()->pq('input[name="i_tariff_1"]')->val());
		}
		return $this->{__METHOD__};
	}

	/** @return array(string => string|int|float|bool) */
	protected function getPostParameters() {
		return array_merge(parent::getPostParameters(), array(
			self::POST_PARAM__LOCATION_ORIGIN_ID => $this->getLocationOriginId()
			// express — обычная доставка
			// op — оплата получателем
			,self::POST_PARAM__SERVICE => $this->getService()
			,self::POST_PARAM__LOCATION_DESTINATION_ID => $this->getLocationDestinationId()
			,self::POST_PARAM__WEIGHT => $this->getWeight()
		));
	}

	/** @return int */
	private function getLocationDestinationId() {
		return $this->cfg(self::P__LOCATION_DESTINATION_ID);
	}

	/** @return int */
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
			->_prop(self::P__LOCATION_DESTINATION_ID, self::V_INT)
			->_prop(self::P__LOCATION_ORIGIN_ID, self::V_INT)
			->_prop(self::P__SERVICE, self::V_STRING_NE)
			->_prop(self::P__WEIGHT, self::V_FLOAT)
		;
	}
	const _CLASS = __CLASS__;
	const P__LOCATION_DESTINATION_ID = 'location_destination_id';
	const P__LOCATION_ORIGIN_ID = 'location_origin_id';
	const P__SERVICE = 'service';
	const P__WEIGHT = 'weight';
	const POST_PARAM__LOCATION_DESTINATION_ID = 'i_to_1';
	const POST_PARAM__LOCATION_ORIGIN_ID = 'i_from_1';
	const POST_PARAM__SERVICE = 'i_service_1';
	const POST_PARAM__WEIGHT = 'i_weight_1';
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Garantpost_Model_Request_Rate_Light
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}