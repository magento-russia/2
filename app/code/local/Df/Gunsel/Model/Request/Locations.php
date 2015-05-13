<?php
class Df_Gunsel_Model_Request_Locations extends Df_Gunsel_Model_Request {
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
		/** @var array(string => int) $result */
		$result = array();
		foreach ($this->response()->pq('#sub_select_4 td.sub_select_item') as $domItem) {
			/** @var DOMNode $domItem */
			/** @var phpQueryObject $pqItem */
			$pqItem = df_pq($domItem);
			/** @var string $onclick */
			$onclick = $pqItem->attr('onclick');
			df_assert_string($onclick);
			/** @var int $locationId */
			$locationId = rm_preg_match_int("#\(\'hidden_select_4\'\)\.value \= '(\d+)'#mui", $onclick);
			rm_nat($locationId);
			/** @var string $locationName */
			$locationName = $pqItem->text();
			df_assert_string($locationName);
			$result[mb_strtoupper($locationName)] = $locationId;
		}
		return $result;
	}

	const _CLASS = __CLASS__;
	/** @return Df_Gunsel_Model_Request_Locations */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}