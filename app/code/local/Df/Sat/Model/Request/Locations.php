<?php
class Df_Sat_Model_Request_Locations extends Df_Sat_Model_Request {
	/** @return array(string => int) */
	public function getLocations() {
		if (!isset($this->{__METHOD__})) {
			/** @var array(string => int) $result */
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

	/** @return array(string => int) */
	private function parseLocations() {
		return array_filter($this->response()->options('select[name="city_from"] option'));
	}

	const _C = __CLASS__;
	/** @return Df_Sat_Model_Request_Locations */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}