<?php
class Df_Cdek_Model_Locator extends Df_Shipping_Model_Locator {
	/**
	 * @override
	 * @return int
	 * @throws Exception
	 */
	public function getResult() {
		if (!isset($this->{__METHOD__})) {
			/** @var int|null $result */
			$result = null;
			/** @var Df_Cdek_Model_Location[] $locationsWithSameName */
			$locationsWithSameName = Df_Cdek_Model_Map::s()->getByCity($this->getCity());
			/** @var string $regionNameNormalized */
			$regionNameNormalized = Df_Cdek_Model_Location::i()->normalizeName($this->getRegionName());
			foreach ($locationsWithSameName as $location) {
				if (
						!$location->hasRegion()
					||
						rm_contains($regionNameNormalized, $location->getRegion())
				) {
					$result = $location->getId();
					break;
				}
			}
			if (!$result) {
				$this->rr() ? $this->throwExceptionInvalidLocation() : df_error();
			}
			df_result_integer($result);
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	const _C = __CLASS__;
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Cdek_Model_Locator
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}