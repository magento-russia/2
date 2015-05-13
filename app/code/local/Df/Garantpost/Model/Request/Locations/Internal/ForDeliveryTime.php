<?php
class Df_Garantpost_Model_Request_Locations_Internal_ForDeliveryTime extends Df_Garantpost_Model_Request_Locations {
	/**
	 * @override
	 * @return array(string => string)
	 */
	protected function getHeaders() {
		return array_merge(parent::getHeaders(), array(
			'Referer' => 'http://www.garantpost.ru/tools/transit/')
		);
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getOptionsSelector() {return '.tarif .frm option';}

	/**
	 * @override
	 * @return string
	 */
	protected function getQueryPath() {return '/tools/transit';}

	/**
	 * @override
	 * @param string $locationName
	 * @return string
	 */
	protected function normalizeLocationName($locationName) {
		df_param_string($locationName, 0);
		$locationName = mb_strtoupper($locationName);
		$locationName =
			strtr(
				$locationName
				,array(
					' А.О.' => ''
					,' КРАЙ' => ''
					,' ОБЛ.' => ''
					,' РЕСП.' => ''
				)
			)
		;
		$locationName = df_trim($locationName, ', ');
		/** @var string $result */
		$result =
			df_a(
				array(
					'СЕВЕРНАЯ ОСЕТИЯ-АЛАНИЯ' => 'СЕВЕРНАЯ ОСЕТИЯ — АЛАНИЯ'
					,'ТЫВА' => 'ТЫВА (ТУВА)'
				)
				,$locationName
				,$locationName
			)
		;
		df_result_string($result);
		return $result;
	}

	/**
	 * @override
	 * @param array $locations
	 * @return array
	 */
	protected function postProcessLocations($locations) {
		/** @var array $result */
		$result =
			parent::postProcessLocations(
				$locations
			)
		;
		/**
		 * Гарантпост записывает название Орловской области с латинскими буквами
		 */
		$result['ОРЛОВСКАЯ'] = df_a($result, 'ОРЛОВCКАЯ');
		df_result_array($result);
		return $result;
	}

	const _CLASS = __CLASS__;
	/** @return Df_Garantpost_Model_Request_Locations_Internal_ForDeliveryTime */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}