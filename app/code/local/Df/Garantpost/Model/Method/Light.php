<?php
abstract class Df_Garantpost_Model_Method_Light extends Df_Garantpost_Model_Method {
	/**
	 * @abstract
	 * @return string
	 */
	abstract protected function getServiceCode();

	/**
	 * @override
	 * @return bool
	 * @throws Exception
	 */
	public function isApplicable() {
		/** @var bool $result */
		$result = parent::isApplicable();
		if ($result) {
			try {
				$this
					->checkCountryDestinationIs(Df_Directory_Helper_Country::ISO_2_CODE__RUSSIA)
					->checkWeightIsLE(31.5)
				;
			}
			catch(Exception $e) {
				if ($this->needDisplayDiagnosticMessages()) {throw $e;} else {$result = false;}
			}
		}
		return $result;
	}

	/**
	 * @override
	 * @return int
	 */
	protected function getCostInRoubles() {return rm_nat($this->getApiRate()->getResult());}

	/**
	 * @override
	 * @return int
	 */
	protected function getTimeOfDeliveryMax() {
		if (!isset($this->{__METHOD__})) {
			// Пока сайт Гарантпоста способен рассчитывать сроки доставки
			// только при отправке из Москвы
			$this->{__METHOD__} =
				!$this->isDeliveryFromMoscow()
				? parent::getTimeOfDeliveryMax()
				: $this->getApiDeliveryTime()->getMax()
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return int
	 */
	protected function getTimeOfDeliveryMin() {
		if (!isset($this->{__METHOD__})) {
			// Пока сайт Гарантпоста способен рассчитывать сроки доставки
			// только при отправке из Москвы
			$this->{__METHOD__} =
				!$this->isDeliveryFromMoscow()
				? parent::getTimeOfDeliveryMin()
				: $this->getApiDeliveryTime()->getMin()
			;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Garantpost_Model_Request_DeliveryTime_Light */
	private function getApiDeliveryTime() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Garantpost_Model_Request_DeliveryTime_Light::i(
				$this->getLocationIdDestinationSpecial($forRate = false)
			);
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Garantpost_Model_Request_Rate_Light */
	private function getApiRate() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Garantpost_Model_Request_Rate_Light::i(array(
				Df_Garantpost_Model_Request_Rate_Light::P__WEIGHT =>
					$this->getRequest()->getWeightInKilogrammes()
				,Df_Garantpost_Model_Request_Rate_Light::P__SERVICE => $this->getServiceCode()
				,Df_Garantpost_Model_Request_Rate_Light::P__LOCATION_ORIGIN_ID =>
					$this->getLocationIdOriginSpecial($forRate = true)
				,Df_Garantpost_Model_Request_Rate_Light::P__LOCATION_DESTINATION_ID =>
					$this->getLocationIdDestinationSpecial($forRate = true)
			));
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param bool $forRate
	 * @return int
	 */
	private function getLocationIdDestinationSpecial($forRate) {
		df_param_boolean($forRate, 0);
		return
			$this->getLocationIdSpecial(
				$this->getRequest()->getDestinationCity()
				,$this->getRequest()->getDestinationRegionId()
				,$isOrigin = false
				,$forRate
			)
		;
	}

	/**
	 * @param string|null $city
	 * @param int $regionId
	 * @param bool $isOrigin
	 * @param bool $forRate
	 * @return int
	 */
	private function getLocationIdSpecial($city, $regionId, $isOrigin, $forRate) {
		if (!is_null($city)) {
			df_param_string($city, 0);
		}
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
		if (is_null($city) && (0 === $regionId)) {
			df_error(
				$isOrigin
				? 'Администратор должен указать город склада магазина.'
				: 'Укажите город или хотя бы область.'
			);
		}
		/** @var int|null $result */
		$result = null;
		if (!is_null($city)) {
			$result = df_a($map, mb_strtoupper($city));
		}
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
			if (is_null($city)) {
				$this->throwExceptionNoCityDestination();
			}
			else {
				/** @var string $location */
				$location =
					!is_null($city)
					? $city
					: df_h()->directory()->getRegionFullNameById($regionId)
				;
				df_assert_string($location);
				/** @var Df_Localization_Model_Morpher_Response $morpher */
				$morpher = Df_Localization_Model_Morpher::s()->getResponseSilent($location);
				/** @var string $phraseEnding */
				$phraseEnding = null;
				if ($morpher) {
					$phraseEnding =
						$isOrigin ? $morpher->getInFormOrigin() : $morpher->getInFormDestination()
					;
				}
				else {
					/** @var string $from */
					$from = !is_null($city) ? 'из населённого пункта' : 'из региона';
					/** @var string $to */
					$to = !is_null($city) ? 'в населённый пункт' : 'в регион';
					$phraseEnding = rm_sprintf('%s %s', $isOrigin ? $from : $to, $location);
				}
				$this->throwException('К сожалению, Гарантпост не отправляет грузы %s.', $phraseEnding);
			}
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
		return
			$this->getLocationIdSpecial(
				$this->getRequest()->getOriginCity()
				,$this->getRequest()->getOriginRegionId()
				,$isOrigin = true
				,$forRate
			)
		;
	}

	const _CLASS = __CLASS__;
}