<?php
class Df_Dellin_Model_Method extends Df_Shipping_Model_Method_Russia {
	/**
	 * @override
	 * @return void
	 * @throws Exception
	 */
	protected function checkApplicability() {
		parent::checkApplicability();
		$this
			->checkCountryOriginIsRussia()
			->checkCountryDestinationIsRussia()
			->checkCityOriginIsNotEmpty()
			->checkCityDestinationIsNotEmpty()
			->checkOriginAndDestinationCitiesAreDifferent()
			->checkLocationIdOrigin()
			->checkLocationIdDestination()
		;
	}

	/**
	 * Обратите внимание, что служба доставки «Деловые Линии»
	 * на самом деле возвращает стоимость доставки в виде дробного числа, с копейками,
	 * например: «737.5».
	 * http://magento-forum.ru/topic/4476/
	 * @override
	 * @used-by Df_Shipping_Model_Method::_getCost()
	 * @return int
	 */
	protected function getCost() {return (int)$this->getApi()->getRate();}

	/**
	 * @override
	 * @used-by Df_Shipping_Model_Method::_getDeliveryTime()
	 * @return int|int[]
	 */
	protected function getDeliveryTime() {return $this->getApi()->getDeliveryTime();}

	/**
	 * @override
	 * @return string
	 */
	protected function getLocationIdDestination() {
		return $this->rr()->getLocatorDestination()->getResult();
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getLocationIdOrigin() {return $this->rr()->getLocatorOrigin()->getResult();}

	/** @return Df_Dellin_Model_Request_Rate */
	private function getApi() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Dellin_Model_Request_Rate::i(array(
				'derivalPoint' => $this->getLocationIdOrigin()
				,'arrivalPoint' => $this->getLocationIdDestination()
				,'sizedWeight' => $this->rr()->getWeightInKilogrammes()
				,'sizedVolume' => $this->rr()->getVolumeInCubicMetres()
				,'statedValue' => $this->rr()->getDeclaredValueInRoubles()
				,'packages' => '0x838FC70BAEB49B564426B45B1D216C15'
			));
		}
		return $this->{__METHOD__};
	}

	/** @used-by Df_Dellin_Model_Collector::getMethods() */
	const _C = __CLASS__;
}