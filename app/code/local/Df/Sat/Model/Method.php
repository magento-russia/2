<?php
abstract class Df_Sat_Model_Method extends Df_Shipping_Model_Method_Ukraine {
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
					,rm_sprintf(
						'%s'
						,$this->formatTimeOfDelivery(
							$timeOfDeliveryMin = 1
							,$timeOfDeliveryMax = 2
						)
					)
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
		$result = parent::isApplicable();
		if ($result) {
			try {
				$this
					->checkCountryOriginIsUkraine()
					->checkCountryDestinationIsUkraine()
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
		return $this->getApi()->getRate();
	}

	/**
	 * @override
	 * @return array
	 */
	protected function getLocations() {
		return Df_Sat_Model_Request_Locations::s()->getLocations();
	}

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
			,'cost' => $this->getRequest()->getDeclaredValueInHryvnias()
			,'description' => ''
			,'doors' => rm_01($this->needDeliverToHome())
			,'sklad' => rm_01($this->getRmConfig()->service()->needGetCargoFromTheShopStore())
			,'shape' => $this->getRequest()->getVolumeInCubicMetres()
			,'weight' => $this->getRequest()->getWeightInKilogrammes()
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

	const _CLASS = __CLASS__;
}