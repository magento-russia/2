<?php
abstract class Df_Ems_Model_Api_Locations_Abstract extends Df_Core_Model_Abstract {
	/**
	 * @abstract
	 * @return string
	 */
	abstract protected function getLocationType();

	/** @return array(string => string) */
	public function getMapFromLocationNameToEmsLocationCode() {
		if (!isset($this->{__METHOD__})) {
			/** @var array(string => string) $result */
			$result = array();
			foreach ($this->getLocationsAsRawArray() as $location) {
				/** @var array(string => string) $location */
				df_assert_array($location);
				/** @var string $locationName */
				$locationName = mb_strtoupper(df_a($location, 'name'));
				df_assert_string($locationName);
				/** @var string $locationCode */
				$locationCode = df_a($location, 'value');
				df_assert_string($locationCode);
				$result[$locationName] = $locationCode;
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return array(array(string => string)) */
	protected function getLocationsAsRawArray() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->getRequest()->getResponseParam('locations');
			df_result_array($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Ems_Model_Request */
	private function getRequest() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Ems_Model_Request::i(array(
				'method' => 'ems.get.locations'
				,'type' => $this->getLocationType()
				,'plain' => rm_bts(true)
			));
		}
		return $this->{__METHOD__};
	}

	const _CLASS = __CLASS__;
}