<?php
abstract class Df_DeliveryUa_Model_Method extends Df_Shipping_Model_Method_Ukraine {
	/**
	 * @abstract
	 * @return bool
	 */
	abstract protected function needDeliverToHome();

	/**
	 * @override
	 * @return void
	 * @throws Exception
	 */
	protected function checkApplicability() {
		parent::checkApplicability();
		$this
			->checkCountryOriginIsUkraine()
			->checkCountryDestinationIsUkraine()
			->checkCityOriginIsNotEmpty()
			->checkCityDestinationIsNotEmpty()
			->checkLocationIdOrigin()
			->checkLocationIdDestination()
		;
	}

	/**
	 * @override
	 * @used-by Df_Shipping_Model_Method::_getCost()
	 * @return float
	 */
	protected function getCost() {
		/** @var float $result */
		$result = $this->getRatePrimary();
		if ($this->configS()->needGetCargoFromTheShopStore()) {
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
	 * @used-by Df_Shipping_Model_Method::_getDeliveryTime()
	 * @return int|int[]
	 */
	protected function getDeliveryTime() {return array(1, 3);}

	/**
	 * @override
	 * @return array
	 */
	protected function getLocations() {return Df_DeliveryUa_Model_Request_Locations::s()->getLocations();}

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
				$this->getApi()->getRateByVolume() * $this->rr()->getVolumeInCubicMetres()
			;
		}
		return $this->{__METHOD__};
	}

	/** @return float */
	private function getRateByWeight() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				$this->getApi()->getRateByWeight() * $this->rr()->getWeightInKilogrammes()
			;
		}
		return $this->{__METHOD__};
	}

	/** @return float */
	private function getRateDoor() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_float(
				df_a(df_a($this->getRateDoorTable(), $this->getRateDoorIndex()), self::$RATE)
			);
		}
		return $this->{__METHOD__};
	}

	/** @return int */
	private function getRateDoorIndex() {
		if (!isset($this->{__METHOD__})) {
			/** @var int $result */
			$result = count($this->getRateDoorTable()) - 1;
			foreach ($this->getRateDoorTable() as $index => $rateConditions) {
				/** @var int $index */
				df_assert_integer($index);
				/** @var array $rateConditions */
				df_assert_array($rateConditions);
				/** @var float $maxWeight */
				$maxWeight = df_a($rateConditions, self::$MAX_WEIGHT);
				df_assert_float($maxWeight);
				/** @var float $maxVolume */
				$maxVolume = df_a($rateConditions, self::$MAX_VOLUME);
				df_assert_float($maxVolume);
				if (
						($maxVolume >= $this->rr()->getVolumeInCubicMetres())
					&&
						($maxWeight >= $this->rr()->getWeightInKilogrammes())
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

	/** @return array(array(string => float)) */
	private function getRateDoorTable() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_map('array_combine', array(
				array(0.3, 100, 45)
				,array(1, 250, 55)
				,array(2, 500, 65)
				,array(3, 750, 75)
				,array(4, 1000, 85)
				,array(5, 1250, 95)
				,array(6, 1500, 105)
				,array(7, 2000, 125)
				,array(10, 2500, 150)
				,array(12, 3000, 185)
				,array(14, 3500, 220)
				,array(16, 4000, 260)
				,array(20, 5000, 300)
			), array(), array(array(self::$MAX_VOLUME, self::$MAX_WEIGHT, self::$RATE)));
		}
		return $this->{__METHOD__};
	}

	/** @var string */
	private static $MAX_VOLUME = 'max_volume';
	/** @var string */
	private static $MAX_WEIGHT = 'max_weight';
	/** @var string */
	private static $RATE = 'rate';

	/** @return float */
	private function getRatePrimary() {
		// Будем считать, что у нас только 1 место (т.е. что все товары запакованы в один ящик)
		return max(8, $this->getRateByVolume(), $this->getRateByWeight());
	}
}