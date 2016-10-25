<?php
abstract class Df_Ems_Api_Locations extends Df_Core_Model {
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
				$locationName = mb_strtoupper(dfa($location, 'name'));
				df_assert_string($locationName);
				/** @var string $locationCode */
				$locationCode = dfa($location, 'value');
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

	/** @return Df_Ems_Request */
	private function getRequest() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Ems_Request::i(array(
				'method' => 'ems.get.locations'
				,'type' => $this->getLocationType()
				,'plain' => df_bts(true)
			));
		}
		return $this->{__METHOD__};
	}
}