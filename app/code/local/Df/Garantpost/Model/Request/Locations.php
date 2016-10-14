<?php
abstract class Df_Garantpost_Model_Request_Locations extends Df_Garantpost_Model_Request {
	/**
	 * @abstract
	 * @return string
	 */
	abstract protected function getOptionsSelector();

	/**
	 * @abstract
	 * @param string $locationName
	 * @return string
	 */
	abstract protected function normalizeLocationName($locationName);

	/** @return array(string => int) */
	public function getResponseAsArray() {
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

	/**
	 * @param array(string => int) $locations
	 * @return array(string => int)
	 */
	protected function postProcessLocations($locations) {
		// У Чеченской республики отсутствует код
		return array_filter($locations);
	}

	/** @return array(string => int) */
	private function parseLocations() {
		/** @var array(string => string) $options */
		$options = $this->response()->options($this->getOptionsSelector());
		/** @var array(string => int) $locations */
		$locations = array();
		foreach ($options as $locationName => $locationId) {
			/** @var string $locationName */
			/** @var int $locationId */
			$locationName = $this->normalizeLocationName($locationName);
			$locations[$locationName]= $locationId;
		}
		return $this->postProcessLocations($locations);
	}

	const _C = __CLASS__;
}