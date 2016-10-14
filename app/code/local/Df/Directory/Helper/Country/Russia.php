<?php
class Df_Directory_Helper_Country_Russia extends Df_Directory_Helper_Country {
	/** @return string[] */
	public function getFederalCities() {return array('Москва', 'Санкт-Петербург');}

	/** @return array(string => Df_Directory_Model_Region) */
	public function getMapFromCenterToRegion() {
		if (!isset($this->{__METHOD__})) {
			/** @var array(string => Df_Directory_Model_Region) $result */
			$result = array();
			foreach (df_h()->directory()->getRussianRegions() as $region) {
				/** @var Df_Directory_Model_Region $region */
				/** @var string $centerName */
				$centerName = $region->getData('df_capital');
				// У Московской и Ленинградской областей как бы и нет столиц
				if (!is_null($centerName)) {
					df_assert_string($centerName);
					/** @var string $centerNameNormalized */
					$centerNameNormalized = mb_strtoupper($centerName);
					df_assert_string($centerNameNormalized);
					$result[$centerNameNormalized] = $region;
				}
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param string $locationName
	 * @return bool
	 */
	public function isRegionalCenter($locationName) {
		df_param_string($locationName, 0);
		return !is_null(dfa($this->getMapFromCenterToRegion(), mb_strtoupper($locationName)));
	}

	/** @return Df_Directory_Helper_Country_Russia */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}