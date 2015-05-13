<?php
class Df_NightExpress_Model_Request_Locations extends Df_NightExpress_Model_Request {
	/** @return array(string => string) */
	public function getLocations() {
		if (!isset($this->{__METHOD__})) {
			/** @var array(string => string) $result */
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
	 * @return string
	 */
	protected function getQueryPath() {return '/calculator';}

	/** @return array(string => string) */
	private function parseLocations() {
		/** @var array(string => string) $result */
		$result = array();
		/** @var array(string => string) $options */
		$options = $this->response()->options('#city_in option', $idIsString = true);
		foreach ($options as $locationName => $locationId) {
			/** @var string $locationName */
			/** @var string $locationId */
			df_assert_string($locationId);
			if ('0' !== $locationId) {
				/** @var string $locationName */
				$locationName = df_trim($locationName, '[]-');
				$result[$locationName] = $locationId;
			}
		}
		return $result;
	}

	const _CLASS = __CLASS__;
	/** @return Df_NightExpress_Model_Request_Locations */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}