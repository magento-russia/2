<?php
class Df_EuroExpress_Model_Request_Locations extends Df_EuroExpress_Model_Request {
	/** @return mixed[] */
	public function getLocations() {
		if (!isset($this->{__METHOD__})) {
			/** @var mixed[] $result */
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
		/** @var array(string => int) $result */
		$result = array();
		foreach ($this->response()->options('#Select1 option') as $locationName => $locationId) {
			/** @var string $locationName */
			/** @var int $locationId */
			if (0 < $locationId) {
				$result[$locationName] = $locationId;
			}
		}
		return $result;
	}

	const _CLASS = __CLASS__;
	/** @return Df_EuroExpress_Model_Request_Locations */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}