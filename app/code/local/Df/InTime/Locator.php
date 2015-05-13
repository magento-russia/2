<?php
class Df_InTime_Locator extends Df_Shipping_Locator {
	/**
	 * @used-by map()
	 * @param int $type
	 * @return array(string => int)
	 */
	protected function _map($type) {
		return self::cleanParenthesesK(array_column($this->response($type)->json(), 'id', 'town'));
	}

	/**
	 * @used-by Df_InTime_Model_Method::getLocationIdDestination()
	 * @used-by Df_InTime_Model_Method::getLocationIdOrigin()
	 * @param string $location
	 * @param string $regionName
	 * @return int|null
	 */
	public static function find($location, $regionName) {
		df_param_string_not_empty($location, 0);
		df_param_string_not_empty($regionName, 1);
		/** @var int $regionId */
		$regionId = df_a(Df_InTime_Data::$regionIds, mb_strtoupper($regionName));
		df_assert_gt0($regionId);
		/** @var string $locationUc */
		$locationUc = mb_strtoupper($location);
		$result = self::_find(__CLASS__, $regionId, $locationUc);
		if (!$result) {
			$result = self::_find(__CLASS__, $regionId, $locationUc . ' ЦЕНТР');
		}
		if (!$result) {
			$result =  self::_find(__CLASS__, $regionId, $locationUc, $starts = true);
		}
		return $result;
	}

	/**
	 * @param int $regionId
	 * @used-by _map()
	 * @return Df_Shipping_Model_Response
	 */
	private static function response($regionId) {
		/** @var array(int => Df_Shipping_Model_Response) $cache */
		static $cache;
		if (!isset($cache[$regionId])) {
			/** @var Df_Shipping_Model_Request $request */
			$request = new Df_Shipping_Model_Request(array(
				Df_Shipping_Model_Request::P__QUERY_HOST => 'www.intime.ua'
				,Df_Shipping_Model_Request::P__REQUEST_METHOD => 'POST'
				,Df_Shipping_Model_Request::P__QUERY_PATH => '/calc/'
				,Df_Shipping_Model_Request::P__POST_PARAMS => array('region_to' => $regionId)
			));
			$cache[$regionId] = $request->response();
		}
		return $cache[$regionId];
	}
}