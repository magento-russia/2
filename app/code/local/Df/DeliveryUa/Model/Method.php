<?php
abstract class Df_DeliveryUa_Model_Method extends Df_Shipping_Model_Method_Ukraine {
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
		return
			implode(
				' '
				,array(
					rm_sprintf('%s:', parent::getMethodTitle())
					,rm_sprintf(
						'%s'
						,$this->formatTimeOfDelivery(
							$timeOfDeliveryMin = 1
							,$timeOfDeliveryMax = 3
						)
					)
				)
			)
		;
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
		$result = $this->getRatePrimary();
		if ($this->getRmConfig()->service()->needGetCargoFromTheShopStore()) {
			$result += $this->getRateDoor();
		}
		if ($this->needDeliverToHome()) {
			$result += $this->getRateDoor();
		}
		// Стоимость оформления багажа:	1.0 грн/место.
		$result += 1;
		return $result;
	}

	/**
	 * @override
	 * @return array
	 */
	protected function getLocations() {
		return Df_DeliveryUa_Model_Request_Locations::s()->getLocations();
	}

	/** @return Df_DeliveryUa_Model_Request_Rate */
	private function getApi() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_DeliveryUa_Model_Request_Rate::i(
				$this->getLocationIdOrigin(), $this->getLocationIdDestination()
			);
		}
		return $this->{__METHOD__};
	}

	/** @return float */
	private function getRateByVolume() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
					$this->getApi()->getRateByVolume()
				*
					$this->getRequest()->getVolumeInCubicMetres()
			;
		}
		return $this->{__METHOD__};
	}

	/** @return float */
	private function getRateByWeight() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
					$this->getApi()->getRateByWeight()
				*
					$this->getRequest()->getWeightInKilogrammes()
			;
		}
		return $this->{__METHOD__};
	}

	/** @return float */
	private function getRateDoor() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_float(
				df_a(df_a(self::$_rateDoorTable, $this->getRateDoorIndex()), self::RATE)
			);
		}
		return $this->{__METHOD__};
	}

	/** @return int */
	private function getRateDoorIndex() {
		if (!isset($this->{__METHOD__})) {
			/** @var int $result */
			$result = count(self::$_rateDoorTable) - 1;
			foreach (self::$_rateDoorTable as $index => $rateConditions) {
				/** @var int $index */
				df_assert_integer($index);
				/** @var array $rateConditions */
				df_assert_array($rateConditions);
				/** @var float $maxWeight */
				$maxWeight = df_a($rateConditions, self::MAX_WEIGHT);
				df_assert_float($maxWeight);
				/** @var float $maxVolume */
				$maxVolume = df_a($rateConditions, self::MAX_VOLUME);
				df_assert_float($maxVolume);
				if (
						($maxVolume >= $this->getRequest()->getVolumeInCubicMetres())
					&&
						($maxWeight >= $this->getRequest()->getWeightInKilogrammes())
				) {
					$result = $index;
					break;
				}
			}
			df_result_integer($result);
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @var array */
	private static $_rateDoorTable =
		array(
			array(
				self::MAX_VOLUME => 0.3
				,self::MAX_WEIGHT => 100
				,self::RATE => 45
			)
			,array(
				self::MAX_VOLUME => 1
				,self::MAX_WEIGHT => 250
				,self::RATE => 55
			)
			,array(
				self::MAX_VOLUME => 2
				,self::MAX_WEIGHT => 500
				,self::RATE => 65
			)
			,array(
				self::MAX_VOLUME => 3
				,self::MAX_WEIGHT => 750
				,self::RATE => 75
			)
			,array(
				self::MAX_VOLUME => 4
				,self::MAX_WEIGHT => 1000
				,self::RATE => 85
			)
			,array(
				self::MAX_VOLUME => 5
				,self::MAX_WEIGHT => 1250
				,self::RATE => 95
			)
			,array(
				self::MAX_VOLUME => 6
				,self::MAX_WEIGHT => 1500
				,self::RATE => 105
			)
			,array(
				self::MAX_VOLUME => 7
				,self::MAX_WEIGHT => 2000
				,self::RATE => 125
			)
			,array(
				self::MAX_VOLUME => 10
				,self::MAX_WEIGHT => 2500
				,self::RATE => 150
			)
			,array(
				self::MAX_VOLUME => 12
				,self::MAX_WEIGHT => 3000
				,self::RATE => 185
			)
			,array(
				self::MAX_VOLUME => 14
				,self::MAX_WEIGHT => 3500
				,self::RATE => 220
			)
			,array(
				self::MAX_VOLUME => 16
				,self::MAX_WEIGHT => 4000
				,self::RATE => 260
			)
			,array(
				self::MAX_VOLUME => 20
				,self::MAX_WEIGHT => 5000
				,self::RATE => 300
			)
		)
	;
	const MAX_VOLUME = 'max_volume';
	const MAX_WEIGHT = 'max_weight';
	const RATE = 'rate';

	/** @return float */
	private function getRatePrimary() {
		// Будем считать, что у нас только 1 место (т.е. что все товары запакованы в один ящик)
		return max(8, $this->getRateByVolume(), $this->getRateByWeight());
	}

	const _CLASS = __CLASS__;
}