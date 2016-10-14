<?php
abstract class Df_Garantpost_Model_Method_Light extends Df_Garantpost_Model_Method {
	/**
	 * @abstract
	 * @return string
	 */
	abstract protected function getServiceCode();

	/**
	 * @override
	 * @return void
	 * @throws Exception
	 */
	protected function checkApplicability() {
		parent::checkApplicability();
		$this
			->checkCountryDestinationIs(Df_Directory_Helper_Country::ISO_2_CODE__RUSSIA)
			->checkCityOriginIsNotEmpty()
			->checkCityDestinationIsNotEmpty()
			->checkWeightIsLE(31.5)
		;
	}

	/**
	 * @override
	 * @used-by Df_Shipping_Model_Method::_getCost()
	 * @return int
	 */
	protected function getCost() {return rm_nat($this->apiRate()->getResult());}

	/**
	 * Пока сайт Гарантпоста способен рассчитывать сроки доставки
	 * только при отправке из Москвы.
	 * @override
	 * @used-by Df_Shipping_Model_Method::_getDeliveryTime()
	 * @return int|int[]
	 */
	protected function getDeliveryTime() {
		return
			!$this->isDeliveryFromMoscow()
			? 0
			: array($this->apiTime()->getMin(), $this->apiTime()->getMax())
		;
	}

	/** @return Df_Garantpost_Model_Request_Rate_Light */
	private function apiRate() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Garantpost_Model_Request_Rate_Light::i(array(
				Df_Garantpost_Model_Request_Rate_Light::P__WEIGHT => $this->rr()->getWeightInKilogrammes()
				,Df_Garantpost_Model_Request_Rate_Light::P__SERVICE => $this->getServiceCode()
				,Df_Garantpost_Model_Request_Rate_Light::P__LOCATION_ORIGIN_ID =>
					$this->getLocationIdOriginSpecial($forRate = true)
				,Df_Garantpost_Model_Request_Rate_Light::P__LOCATION_DESTINATION_ID =>
					$this->getLocationIdDestinationSpecial($forRate = true)
			));
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Garantpost_Model_Request_DeliveryTime_Light */
	private function apiTime() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Garantpost_Model_Request_DeliveryTime_Light::i(
				$this->getLocationIdDestinationSpecial($forRate = false)
			);
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param bool $forRate
	 * @return int
	 */
	private function getLocationIdDestinationSpecial($forRate) {
		df_param_boolean($forRate, 0);
		return $this->getLocationIdSpecial(
			$this->rr()->getDestinationCity()
			,$this->rr()->getDestinationRegionId()
			,$isOrigin = false
			,$forRate
		);
	}

	/**
	 * @param string|null $city
	 * @param int $regionId
	 * @param bool $isOrigin
	 * @param bool $forRate
	 * @return int
	 */
	private function getLocationIdSpecial($city, $regionId, $isOrigin, $forRate) {
		$regionId = rm_nat0($regionId);
		df_param_integer($regionId, 1);
		df_param_boolean($isOrigin, 2);
		df_param_boolean($forRate, 3);
		/** @var array $map */
		$map =
			$forRate
			? Df_Garantpost_Model_Request_Locations_Internal_ForRate::s()->getResponseAsArray()
			: Df_Garantpost_Model_Request_Locations_Internal_ForDeliveryTime::s()->getResponseAsArray()
		;
		df_assert_array($map);
		/** @var int|null $result */
		$result = null;
		$result = df_a($map, mb_strtoupper($city));
		/** @var string|null $regionName */
		$regionName =
			(0 !== $regionId)
			? df_h()->directory()->getRegionNameById($regionId)
			: null
		;
		if (is_null($result)) {
			if (0 !== $regionId) {
				$result = df_a($map, mb_strtoupper($regionName));
			}
		}
		if (is_null($result)) {
			/** @var Df_Localization_Morpher_Response $morpher */
			$morpher = Df_Localization_Morpher::s()->getResponseSilent($city);
			$this->throwException('К сожалению, Гарантпост не отправляет грузы %s.',
				$morpher
				? ($isOrigin ? $morpher->getInFormOrigin() : $morpher->getInFormDestination())
				: implode(' ', array($isOrigin ? 'из населённого пункта' : 'в населённый пункт', $city))
			);
		}
		df_result_integer($result);
		return $result;
	}

	/**
	 * @param bool $forRate
	 * @return int
	 */
	private function getLocationIdOriginSpecial($forRate) {
		df_param_boolean($forRate, 0);
		return $this->getLocationIdSpecial(
			$this->rr()->getOriginCity()
			,$this->rr()->getOriginRegionId()
			,$isOrigin = true
			,$forRate
		);
	}
}