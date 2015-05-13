<?php
class Df_Ems_Model_Locator extends Df_Shipping_Model_Locator {
	/**
	 * @override
	 * @return string
	 * @throws Exception
	 */
	public function getResult() {
		if (!isset($this->{__METHOD__})) {
			/** @var string|null $result */
			$result = null;
			if (Df_Ems_Model_Request::NO_INTERNET) {
				$result = 'city--moskva';
			}
			else {
				// Сначала пробуем найти город
				if (!is_null($this->getCity())) {
					$result =
						df_a(
							df_h()->ems()->api()->cities()->getMapFromLocationNameToEmsLocationCode()
							,mb_strtoupper($this->getCity())
						)
					;
				}
				if (is_null($result)) {
					// Город не найден. Теперь ищем субъект РФ.
					if (0 !== rm_nat0($this->getRegionId())) {
						$result =
							df_a(
								df_h()->ems()->api()->regions()->getMapFromMagentoRegionIdToEmsRegionId()
								,$this->getRegionId()
							)
						;
					}
				}
				if (is_null($result)) {
					// Субъект РФ не найден. Ищем страну.
					if (!is_null($this->getCountryName())) {
						$result =
							df_a(
								df_h()->ems()->api()->countries()
									->getMapFromLocationNameToEmsLocationCode()
								,mb_strtoupper($this->getCountryName())
							)
						;
						if (is_null($result)) {
							if (
									$this->getCountry()->isRussia()
								&&
									(0 === rm_nat0($this->getRegionId()))
							) {
								$this->getRequest()->throwException('Укажите область.');
							}
							else {
								df_error(
									'К сожалению, мы не можем определить указанное Вами место доставки.'
									."\nМожет быть, Вы неправильно указали город, область или страну?"
								);
							}
						}
					}
				}
				if (!is_string($result)) {
					$this->throwExceptionInvalidLocation();
				}
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Ems_Model_Locator
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}