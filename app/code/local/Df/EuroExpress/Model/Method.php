<?php
class Df_EuroExpress_Model_Method extends Df_Shipping_Model_Method_Ukraine {
	/**
	 * @override
	 * @return string
	 */
	public function getMethod() {return 'internet-shop';}

	/**
	 * @override
	 * @return string
	 */
	public function getMethodTitle() {
		/** @var string $result */
		$result =
			rm_sprintf(
				'%s'
				,$this->formatTimeOfDelivery(
					$timeOfDeliveryMin = 1
					,$timeOfDeliveryMax = 2
				)
			)
		;
		df_result_string($result);
		return $result;
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
		$result = 0.0;
		/**
		 * Добавляем стоимость приёма наложенного платежа
		 */
		$result += ceil(0.01 * $this->getRequest()->getDeclaredValueInHryvnias());
		/** @var float $rateByWeight */
		$rateByWeight = $this->getFactorWeight() * $this->getRequest()->getWeightInKilogrammes();
		df_assert_float($rateByWeight);
		/** @var float $rateByVolume */
		$rateByVolume = $this->getFactorVolume() * $this->getRequest()->getVolumeInCubicMetres();
		df_assert_float($rateByVolume);
		$result += max($rateByWeight, $rateByVolume);
		return $result;
	}

	/**
	 * @override
	 * @return array
	 */
	protected function getLocations() {
		return Df_EuroExpress_Model_Request_Locations::s()->getLocations();
	}

	/** @return float */
	private function getFactorVolume() {return $this->isItLight() ? 250 : 325;}

	/** @return float */
	private function getFactorWeight() {return $this->isItLight() ? 1.0 : 1.3;}

	/** @return float */
	private function getCostInsurance() {
		return max(1, 0.004 * $this->getRequest()->getDeclaredValueInHryvnias());
	}

	/** @return bool */
	private function isItLight() {return 30 >= $this->getRequest()->getWeightInKilogrammes();}

	const _CLASS = __CLASS__;

}