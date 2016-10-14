<?php
class Df_PonyExpress_Model_Locator extends Df_Shipping_Model_Locator {
	/**
	 * @override
	 * @return string
	 * @throws Exception
	 */
	public function getResult() {
		if (!isset($this->{__METHOD__})) {
			/** @var string|null $result */
			$result = null;
			/** @var Df_PonyExpress_Model_Location[] $locationsWithSameName */
			$locationsWithSameName = Df_PonyExpress_Model_Map::s()->getByCity($this->getCity());
			/** @var string $regionNameNormalized */
			$regionNameNormalized =
				Df_PonyExpress_Model_Location::i()->normalizeName($this->getRegionName())
			;
			foreach ($locationsWithSameName as $location) {
				if (
						!$location->hasRegion()
					||
						/**
						 * Раньше условие было сформулировано неверно:
						 * if ($region === $this->getRegionName())
						 * Однако для Украины справочник областей в Российской сборке Magento
						 * содержит слово «область» в названии области (например: «Винницкая область»),
						 * а справочник, получаемый с сайта PONY EXPRESS — не содержат
						 * (например: «Винницкая»)
						 */
						rm_contains($regionNameNormalized, $location->getRegion())
				) {
					$result = $location->asText();
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
		return strtr($locationName, array('ё' => 'е', 'Ё' => 'Е'));
	}

	const _C = __CLASS__;
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_PonyExpress_Model_Locator
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}