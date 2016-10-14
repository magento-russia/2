<?php
class Df_Garantpost_Model_Request_DeliveryTime_Light extends Df_Garantpost_Model_Request_DeliveryTime {
	/** @return int */
	public function getMax() {
		/** @var int $result */
		$result = df_a($this->getResultAsInterval(), 1, 0);
		df_result_integer($result);
		return $result;
	}

	/** @return int */
	public function getMin() {
		/** @var int $result */
		$result = df_a($this->getResultAsInterval(), 0, 0);
		df_result_integer($result);
		return $result;
	}

	/** @return int[] */
	public function getResultAsInterval() {
		if (!isset($this->{__METHOD__})) {
			/** @var phpQueryObject $pqDeliveryTime */
			$pqDeliveryTime = $this->response()->pq('#body_min_height table:first tr.text:last td:last');
			/** @var string $deliveryTimeAsText */
			$deliveryTimeAsText = df_trim($pqDeliveryTime->text());
			df_assert_string($deliveryTimeAsText);
			$this->{__METHOD__} = rm_int(explode('-', $deliveryTimeAsText));
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return array(string => string)
	 */
	protected function getHeaders() {
		return array('Referer' => 'http://www.garantpost.ru/tools/transit/') + parent::getHeaders();
	}

	/** @return array(string => string|int|float|bool) */
	protected function getPostParameters() {
		return array_merge(parent::getPostParameters(),array(
			'if_submit' => 1
			,self::POST_PARAM__LOCATION_DESTINATION_ID => $this->getLocationDestinationId()
		));
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getQueryPath() {return '/tools/transit/';}

	/** @return int */
	private function getLocationDestinationId() {
		return $this->cfg(self::P__LOCATION_DESTINATION_ID);
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__LOCATION_DESTINATION_ID, RM_V_INT);
	}
	const _C = __CLASS__;
	const P__LOCATION_DESTINATION_ID = 'location_destination_id';
	const POST_PARAM__LOCATION_DESTINATION_ID = 'city';
	/**
	 * @static
	 * @param int $locationDestinationId
	 * @return Df_Garantpost_Model_Request_DeliveryTime_Light
	 */
	public static function i($locationDestinationId) {return new self(array(
		self::P__LOCATION_DESTINATION_ID => $locationDestinationId
	));}
}