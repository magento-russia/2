<?php
abstract class Df_Autolux_Model_Method extends Df_Shipping_Model_Method_Ukraine {
	/**
	 * @abstract
	 * @return bool
	 */
	abstract protected function needDeliverToHome();

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
		$result = $this->getCostInsurance() + $this->getRatePrimary();
		if ($this->needDeliverToHome()) {
			$result += 50;
		}
		if ($this->getRmConfig()->service()->needGetCargoFromTheShopStore()) {
			$result += 50;
		}
		// Стоимость отправки оплаты за груз составляет 14 грн. и 1% от суммы.
		$result += (14 + (0.01 * $result));
		return $result;
	}

	/**
	 * @override
	 * @return array
	 */
	protected function getLocations() {
		return Df_Autolux_Model_Request_Locations::s()->getLocations();
	}

	/** @return Df_Autolux_Model_Request_Rate */
	private function getApi() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Autolux_Model_Request_Rate::i($this->getQueryParams());
		}
		return $this->{__METHOD__};
	}

	/** @return float */
	private function getCostInsurance() {
		return max(1, 0.01 * $this->getRequest()->getDeclaredValueInHryvnias());
	}

	/** @return array */
	private function getQueryParams() {
		return array(
			'arrival' => $this->getLocationIdDestination()
			,'departure' => $this->getLocationIdOrigin()
		);
	}

	/** @return float */
	private function getRatePrimary() {
		/** @var float $rateByWeight */
		$rateByWeight =
			max(1, $this->getApi()->getFactorWeight() * $this->getRequest()->getWeightInKilogrammes())
		;
		/** @var float $rateByVolume */
		$rateByVolume =
			max(1, $this->getApi()->getFactorVolume() * $this->getRequest()->getVolumeInCubicMetres())
		;
		return ceil(max($rateByWeight, $rateByVolume));
	}

	const _CLASS = __CLASS__;
}