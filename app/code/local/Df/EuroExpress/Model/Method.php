<?php
class Df_EuroExpress_Model_Method extends Df_Shipping_Model_Method_Ukraine {
	/**
	 * @override
	 * @return string
	 */
	public function getMethod() {return 'internet-shop';}

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
		return
				// стоимость приёма наложенного платежа
				ceil(0.01 * $this->rr()->getDeclaredValueInHryvnias())
			+
				max(
					$this->getFactorWeight() * $this->rr()->getWeightInKilogrammes()
					, $this->getFactorVolume() * $this->rr()->getVolumeInCubicMetres()
				)
		;
	}

	/**
	 * @override
	 * @used-by Df_Shipping_Model_Method::_getDeliveryTime()
	 * @return int|int[]
	 */
	protected function getDeliveryTime() {return array(1, 2);}

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
	private function getCostInsurance() {return max(1, 0.004 * $this->rr()->getDeclaredValueInHryvnias());}

	/** @return bool */
	private function isItLight() {return 30 >= $this->rr()->getWeightInKilogrammes();}
}