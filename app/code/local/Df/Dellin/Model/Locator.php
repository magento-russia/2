<?php
class Df_Dellin_Model_Locator extends Df_Shipping_Model_Locator {
	/**
	 * @override
	 * @return string
	 * @throws Exception
	 */
	public function getResult() {
		if (!isset($this->{__METHOD__})) {
			/** @var string|null $result */
			$result = null;
			/** @var Df_Dellin_Model_Location[] $locationsWithSameName */
			$locationsWithSameName = Df_Dellin_Model_Map::s()->getByCity($this->getCity());
			/** @var string $regionNameNormalized */
			$regionNameNormalized =
				Df_Dellin_Model_Location::i()->normalizeName($this->getRegionName())
			;
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
			if (is_null($result)) {
				$this->throwExceptionInvalidLocation();
			}
			df_result_string($result);
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * Заметил, что калькулятор PONY EXPRESS использует названия населенных пунктов без буквы «ё»
	 * @param string $locationName
	 * @return string
	 */
	private function normalizeLocationName($locationName) {
		/** @var string $result */
		$result =
			strtr(
				$locationName
				,array(
					'ё' => 'е'
					,'Ё' => 'Е'
				)
			)
		;
		return $result;
	}

	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Dellin_Model_Locator
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}