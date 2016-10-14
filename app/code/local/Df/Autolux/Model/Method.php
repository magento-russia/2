<?php
abstract class Df_Autolux_Model_Method extends Df_Shipping_Model_Method_Ukraine {
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
		$result = $this->getCostInsurance() + $this->getRatePrimary();
		if ($this->needDeliverToHome()) {
			$result += 50;
		}
		if ($this->configS()->needGetCargoFromTheShopStore()) {
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
	protected function getLocations() {return Df_Autolux_Model_Request_Locations::s()->getLocations();}

	/** @return Df_Autolux_Model_Request_Rate */
	private function getApi() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Autolux_Model_Request_Rate::i($this->getQueryParams());
		}
		return $this->{__METHOD__};
	}

	/** @return float */
	private function getCostInsurance() {return max(1, 0.01 * $this->rr()->getDeclaredValueInHryvnias());}

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
		$rateByWeight = max(1, $this->getApi()->getFactorWeight() * $this->rr()->getWeightInKilogrammes());
		/** @var float $rateByVolume */
		$rateByVolume = max(1, $this->getApi()->getFactorVolume() * $this->rr()->getVolumeInCubicMetres());
		return ceil(max($rateByWeight, $rateByVolume));
	}
}