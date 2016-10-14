<?php
class Df_Autolux_Model_Request_Locations extends Df_Autolux_Model_Request {
	/** @return int[] */
	public function getLocations() {
		if (!isset($this->{__METHOD__})) {
			/** @var int[] $result */
			/** @var string $cacheKey */
			$cacheKey = $this->getCache()->makeKey(array($this, __FUNCTION__));
			$result = $this->getCache()->loadDataArray($cacheKey);
			if (!is_array($result)) {
				$result = $this->parseLocations();
				$this->getCache()->saveDataArray($cacheKey, $result);
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return array(string => string)
	 */
	protected function getQueryParams() {
		return array_merge(parent::getQueryParams(), array('language' => 'ru'));
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getQueryPath() {return '/Autolux/inc/Pages/PatternStd/img/cities.php';}

	/** @return int[] */
	private function parseLocations() {
		/** @var int[] $result */
		$result = array();
		/** @var string $pattern */
		$pattern = '#<option value=\'(\d+)\'>([^<]+)</option>#mui';
		/** @var string[][] $matches */
		$matches = array();
		/** @var int|bool $matchingResult */
		$matchingResult =
			preg_match_all(
				$pattern
				,$this->response()->text()
				,$matches
				,$flags = PREG_SET_ORDER
			)
		;
		df_assert_gt(1, $matchingResult);
		df_assert_array($matches);
		foreach ($matches as $match) {
			/** @var string[] $match */
			df_assert_array($match);
			/** @var int $locationId */
			$locationId = rm_nat0(df_a($match, 1));
			if (0 === $locationId) {
				continue;
			}
			/** @var string $locationNameRaw */
			$locationNameRaw = df_a($match, 2);
			df_assert_string_not_empty($locationNameRaw);
			/** @var string $locationName */
			$locationName = rm_first(df_csv_parse($locationNameRaw));
			df_assert_string_not_empty($locationName);
			$locationName = mb_strtoupper($locationName);
			if (!isset($result[$locationName])) {
				$result[$locationName] = $locationId;
			}
		}
		return $result;
	}

	/**
	 * @used-by Df_Autolux_Model_Method::getLocations()
	 * @return Df_Autolux_Model_Request_Locations
	 */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}