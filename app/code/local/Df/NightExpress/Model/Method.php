<?php
abstract class Df_NightExpress_Model_Method extends Df_Shipping_Model_Method_Ukraine {
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
					,rm_sprintf('%s', $this->formatTimeOfDelivery(1))
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
		$result = $this->getApi()->getRate();
		if ($this->getRmConfig()->service()->needGetCargoFromTheShopStore()) {
			$result += 25;
		}
		return $result;
	}

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
		$result =
			array(
				'to_door' => rm_bts($this->needDeliverToHome())
				,'weight' =>
					// Как ни странно, надо умножать именно на 100
					100 * $this->getRequest()->getWeightInKilogrammes()
				,'count' => $this->getRequest()->getDeclaredValueInHryvnias()
				,'city_in' => $this->getLocationIdDestination()
				,'city_out' => $this->getLocationIdOrigin()
				,'vWeight' =>
					/**
					 * @link http://nightexpress.ua/delivery/answers?lang=ru
					 */
					250 * $this->getRequest()->getVolumeInCubicMetres()
				/**
				 * Здесь единицы тоже странноватые: сантиметры умноженные на 100.
				 * Причем объемный вес рассчитывается из соотношения
				 * 1 кг = 1 кубический дециметр.
				 * Т.к. габариты мы простым образом посчитать не можем,
				 * то подствляем такие, которые соответствуют массе.
				 */
				,'height' => 1000
				,'width1' => 1000
				,'width2' => 1000 * $this->getRequest()->getWeightInKilogrammes()
			)
		;
		return $result;
	}

	const _CLASS = __CLASS__;
}