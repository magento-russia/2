<?php
abstract class Df_InTime_Model_Method extends Df_Shipping_Model_Method_Ukraine {
	/**
	 * @abstract
	 * @return bool
	 */
	abstract protected function needDeliverToHome();

	/**
	 * @override
	 * @return string
	 */
	public function getMethodTitle() {
		return implode(' ', array_filter(array(
			 parent::getMethodTitle()
			,!$this->_applicable ? null : $this->formatTimeOfDelivery($this->getDeliveryTimeInDays())
		)));
	}

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
					->checkCountryOriginIsUkraine()
					->checkCountryDestinationIsUkraine()
					->checkCityOriginIsNotEmpty()
					->checkCityDestinationIsNotEmpty()
				;
				if (!$this->getLocationIdOrigin()) {
					$this->throwExceptionInvalidOrigin();
				}
				if (!$this->getLocationIdDestination()) {
					$this->throwExceptionInvalidDestination();
				}
				$this->_applicable = true;
			}
			catch(Exception $e) {
				if ($this->needDisplayDiagnosticMessages()) {throw $e;} else {$result = false;}
			}
		}
		return $result;
	}

	/**
	 * @override
	 * @return float
	 */
	protected function getCostInHryvnias() {
		/** @var float $result */
		$result = 0.0;
		// Добавляем стоимость оформления заявки
		$result += 10;
		// Добавляем стоимость страховки
		$result += $this->getCostInsurance();
		if (
				$this->getRmConfig()->service()->needGetCargoFromTheShopStore()
			&&
				$this->needDeliverToHome()
		) {
			$result += $this->getRatePrimaryForDoorToDoor();
		}
		else {
			if ($this->getRmConfig()->service()->needGetCargoFromTheShopStore()) {
				$result += $this->getRateCarriage($this->getRequest()->getOriginCity());
			}
			if ($this->needDeliverToHome()) {
				$result += $this->getRateCarriage($this->getRequest()->getDestinationCity());
			}
			/** @var float $rateByWeight */
			$rateByWeight = $this->getFactorWeight() * $this->getRequest()->getWeightInKilogrammes();
			df_assert_float($rateByWeight);
			/** @var float $rateByVolume */
			$rateByVolume = $this->getFactorVolume() * $this->getRequest()->getVolumeInCubicMetres();
			df_assert_float($rateByVolume);
			$result += max($rateByWeight, $rateByVolume);
		}
		return $result;
	}

	/**
	 * @override
	 * @return int
	 */
	protected function getLocationIdDestination() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_InTime_Locator::find(
				$this->getRequest()->getDestinationCity()
				,$this->getRequest()->getDestinationRegionName()
			);
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return int
	 */
	protected function getLocationIdOrigin() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_InTime_Locator::find(
				$this->getRequest()->getOriginCity()
				,$this->getRequest()->getOriginRegionName()
			);
		}
		return $this->{__METHOD__};
	}

	/** @return Df_InTime_Model_Request_Rate */
	private function getApi() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_InTime_Model_Request_Rate::i(
				$this->getLocationIdOrigin(), $this->getLocationIdDestination()
			);
		}
		return $this->{__METHOD__};
	}

	/** @return int|null */
	private function getDeliveryTimeInDays() {
		if (!isset($this->{__METHOD__})) {
			/** @var int|null $result */
			try {
				$result = df()->date()->getNumberOfDaysBetweenTwoDates(
					new Zend_Date(
						df_a($this->getApi()->getResultTable(), 'terms_of_delivery')
						,'dd.MM.yyyy'
					)
					, Zend_Date::now()
				);
			}
			catch (Exception $e) {
				$result = null;
			}
			$this->{__METHOD__} = rm_n_set($result);
		}
		return rm_n_get($this->{__METHOD__});
	}

	/**
	 * 	На представительствах "Ин-Тайм" по приёму/выдаче груза до 30 кг
	 *	и объемом до 0,1м3 действует  единый тариф:
	 *	1грн./кг или 250 грн. за 1м3. "Легкий тариф" действует по всем направлениям.
	 * @return float
	 */
	private function getFactorVolume() {
		return $this->isItLight() ? 250 : rm_float(df_a($this->getRates(), 'm3'));
	}			

	/**
	 * 	На представительствах "Ин-Тайм" по приёму/выдаче груза до 30 кг
	 *	и объемом до 0,1м3 действует  единый тариф:
	 *	1грн./кг или 250 грн. за 1м3. "Легкий тариф" действует по всем направлениям.
	 * @return float
	 */
	private function getFactorWeight() {
		/** @var float $result */
		$result =
				$this->isItLight()
			?
				1
			:
				rm_float(df_a($this->getRates(), 'kg'))
		;
		return $result;
	}		


	/** @return float */
	private function getCostInsurance() {
		return max(1.0, 0.01 * $this->getRequest()->getDeclaredValueInHryvnias());
	}

	/**
	 * @param string $location
	 * @return int
	 */
	private function getRateCarriage($location) {
		df_param_string($location, 0);
		/**
		 * Для простоты считаем,
		 * что в городе доставки и забора груза имеется представительство Ин-Тайма.
		 * По-правильному надо проверять, есть ли представительство, по таблице:
		 * @link http://www.intime.ua/representations/
		 */
		/** @var array $weights */
		$weights =
			array(
				10
				,99
				,499
				,999
				,1999
				,2999
				,4999
			)
		;
		/** @var array $forKiev */
		$forKiev =
			array(
				25,50,60,80,120,200,300,600
			)
		;
		/** @var array $forOtherLocations */
		$forOtherLocations =
			array(
				25,50,60,80,120,160,200,500
			)
		;
		/** @var int $index */
		$index = count($forKiev) - 1;
		foreach ($weights as $currentIndex => $currentMaxWeight) {
			/** @var int $currentIndex */
			/** @var int $currentMaxWeight */
			if ($this->getWeightVolumetric() <= $currentMaxWeight) {
				$index = $currentIndex;
				break;
			}
		}
		df_assert_integer($index);
		/** @var int $result */
		$result =
			df_a(
				('КИЕВ' === mb_strtoupper($location)) ? $forKiev : $forOtherLocations
				,$index
			)
		;
		df_result_integer($result);
		return $result;
	}

	/** @return float */
	private function getRatePrimaryForDoorToDoor() {
		if (!isset($this->{__METHOD__})) {
			/** @var array $zoneRates */
			$zoneRates = df_a(Df_InTime_RatesTable::$data, $this->getZone());
			df_assert_array($zoneRates);
			/** @var array $zoneRate */
			$zoneRate = null;
			/** @var int $minWeight */
			$minWeight = 0;
			if (10 >= $this->getWeightVolumetric()) {
				// Используем тариф для фирменного пакета
				$zoneRate = df_a($zoneRates, 'pack6');
			}
			else {
				foreach ($zoneRates as $currentMinWeight => $currentZoneRate) {
					/** @var int $currentMinWeight */
					/** @var array $zoneRate */
					if (
							($this->getWeightVolumetric() <= df_a($currentZoneRate, 'w'))
						&&
							($this->getWeightVolumetric() >= $currentMinWeight)
					) {
						$minWeight = $currentMinWeight;
						$zoneRate = $currentZoneRate;
						break;
					}
				}
			}
			df_assert_array($zoneRate);
			$this->{__METHOD__} =
					df_a($zoneRate, 'price')
				+
					($this->getWeightVolumetric() - $minWeight) * df_a($zoneRate, 'priceadd')
			;
		}
		return $this->{__METHOD__};
	}

	/** @return array(string => int|float) */
	private function getRates() {
		if (!isset($this->{__METHOD__})) {
			/** @var array(string => int|float) $result */
			$result =
				df_a(
					df_a(
						df_a(
							$this->getApi()->getResultTable()
							,'tariff'
						)
						,$this->getLocationIdOrigin()
					)
					,$this->getLocationIdDestination()
				)
			;
			df_result_array($result);
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return float */
	private function getWeightVolumetric() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				max(
					$this->getRequest()->getWeightInKilogrammes()
					,200 * $this->getRequest()->getVolumeInCubicMetres()
				)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return int */
	private function getZone() {return rm_int(df_a($this->getRates(), 'zone'));}

	/**
	 * 	На представительствах "Ин-Тайм" по приёму/выдаче груза до 30 кг
	 *	и объемом до 0,1м3 действует  единый тариф:
	 *	1грн./кг или 250 грн. за 1м3. "Легкий тариф" действует по всем направлениям.
	 * @return bool
	 */
	private function isItLight() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
					(30 > $this->getRequest()->getWeightInKilogrammes())
				&&
					(0.1 > $this->getRequest()->getVolumeInCubicMetres())
			;
		}
		return $this->{__METHOD__};
	}

	const _CLASS = __CLASS__;

	/**
	 * @used-by isApplicable()
	 * @used-by getMethodTitle()
	 * @var bool
	 */
	private $_applicable = false;
}