<?php
abstract class Df_NightExpress_Model_Method extends Df_Shipping_Model_Method_Ukraine {
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
		$result = $this->getApi()->getRate();
		if ($this->configS()->needGetCargoFromTheShopStore()) {
			$result += 25;
		}
		return $result;
	}

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
	protected function getLocations() {
		return Df_NightExpress_Model_Request_Locations::s()->getLocations();
	}

	/** @return Df_NightExpress_Model_Request_Rate */
	private function getApi() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_NightExpress_Model_Request_Rate::i($this->getPostParams());
		}
		return $this->{__METHOD__};
	}

	/** @return array(string => string|int|float|bool) */
	private function getPostParams() {
		/** @var array(string => string|int|float|bool) $result */
		$result = array(
			'to_door' => rm_bts($this->needDeliverToHome())
			// Как ни странно, надо умножать именно на 100
			,'weight' => 100 * $this->rr()->getWeightInKilogrammes()
			,'count' => $this->rr()->getDeclaredValueInHryvnias()
			,'city_in' => $this->getLocationIdDestination()
			,'city_out' => $this->getLocationIdOrigin()
			/** http://nightexpress.ua/delivery/answers?lang=ru */
			,'vWeight' => 250 * $this->rr()->getVolumeInCubicMetres()
			/**
			 * Здесь единицы тоже странноватые: сантиметры умноженные на 100.
			 * Причем объемный вес рассчитывается из соотношения
			 * 1 кг = 1 кубический дециметр.
			 * Т.к. габариты мы простым образом посчитать не можем,
			 * то подствляем такие, которые соответствуют массе.
			 */
			,'height' => 1000
			,'width1' => 1000
			,'width2' => 1000 * $this->rr()->getWeightInKilogrammes()
		);
		return $result;
	}
}