<?php
class Df_Gunsel_Model_Method extends Df_Shipping_Model_Method_Ukraine {
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
	protected function getCost() {return $this->getApi()->getRate();}

	/**
	 * @override
	 * @used-by Df_Shipping_Model_Method::_getDeliveryTime()
	 * @return int|int[]
	 */
	protected function getDeliveryTime() {return 1;}

	/**
	 * @override
	 * @return array
	 */
	protected function getLocations() {return Df_Gunsel_Model_Request_Locations::s()->getLocations();}

	/** @return Df_Gunsel_Model_Request_Rate */
	private function getApi() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Gunsel_Model_Request_Rate::i($this->getPostParams());
		}
		return $this->{__METHOD__};
	}

	/** @return float */
	private function getCostInsurance() {return max(1, 0.01* $this->rr()->getDeclaredValueInHryvnias());}

	/** @return array */
	private function getPostParams() {
		return array(
			'height' => 50
			,'length' => 50
			,'width' => 50
			,'price' => $this->rr()->getDeclaredValueInHryvnias()
			,'weight' => $this->rr()->getWeightInKilogrammes()
			,'start_city' => mb_strtoupper($this->rr()->getOriginCity())
			,'stop_city' => mb_strtoupper($this->rr()->getDestinationCity())
			,'s1' => 1
			,'s2' => 2
		);
	}
}