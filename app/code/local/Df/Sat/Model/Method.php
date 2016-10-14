<?php
abstract class Df_Sat_Model_Method extends Df_Shipping_Model_Method_Ukraine {
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
	protected function getDeliveryTime() {return array(1, 2);}

	/**
	 * @override
	 * @return array
	 */
	protected function getLocations() {return Df_Sat_Model_Request_Locations::s()->getLocations();}

	/** @return Df_Sat_Model_Request_Rate */
	private function getApi() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Sat_Model_Request_Rate $result */
			$this->{__METHOD__} = Df_Sat_Model_Request_Rate::i($this->getPostParams());
		}
		return $this->{__METHOD__};
	}

	/** @return array */
	private function getPostParams() {
		return array(
			'city_from' => $this->getLocationIdOrigin()
			,'city_to' => $this->getLocationIdDestination()
			,'cost' => $this->rr()->getDeclaredValueInHryvnias()
			,'description' => ''
			,'doors' => rm_01($this->needDeliverToHome())
			,'sklad' => rm_01($this->configS()->needGetCargoFromTheShopStore())
			,'shape' => $this->rr()->getVolumeInCubicMetres()
			,'weight' => $this->rr()->getWeightInKilogrammes()
			,'from' => ''
			,'numbers' => ''
			,'paypack' => 0
			,'paypackw' => 0
			,'paypalet' => 0
			,'phone_from' => ''
			,'phone_to' => ''
			,'shh' => ''
			,'shl' => ''
			,'shw' => ''
			,'to' => ''
		);
	}
}