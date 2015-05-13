<?php
class Df_Garantpost_Model_Request_Locations_Internal_ForRate extends Df_Garantpost_Model_Request_Locations {
	/**
	 * @override
	 * @return array(string => string)
	 */
	protected function getHeaders() {
		return array_merge(parent::getHeaders(), array(
			'Referer' => 'http://www.garantpost.ru/tools/transit/'
		));
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getOptionsSelector() {return 'form.tarif select[name="i_from_1"] option';}

	/**
	 * @override
	 * @return string
	 */
	protected function getQueryPath() {return '/tools/calc';}

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
					' АО' => ''
					,' Г.' => ''
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

	const _CLASS = __CLASS__;
	/** @return Df_Garantpost_Model_Request_Locations_Internal_ForRate */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}