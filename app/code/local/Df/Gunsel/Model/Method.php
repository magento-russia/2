<?php
class Df_Gunsel_Model_Method extends Df_Shipping_Model_Method_Ukraine {
	/**
	 * @override
	 * @return string
	 */
	public function getMethod() {
		return 'standard';
	}

	/**
	 * @override
	 * @return string
	 */
	public function getMethodTitle() {
		return rm_sprintf('%s', $this->formatTimeOfDelivery($timeOfDeliveryMin = 1));
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
		return $this->getApi()->getRate();
	}

	/**
	 * @override
	 * @return array
	 */
	protected function getLocations() {
		return
			Df_Gunsel_Model_Request_Locations::s()->getLocations()
		;
	}

	/** @return Df_Gunsel_Model_Request_Rate */
	private function getApi() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Gunsel_Model_Request_Rate::i($this->getPostParams());
		}
		return $this->{__METHOD__};
	}

	/** @return float */
	private function getCostInsurance() {
		return max(1, 0.01* $this->getRequest()->getDeclaredValueInHryvnias());
	}

	/** @return array */
	private function getPostParams() {
		return array(
			'height' => 50
			,'length' => 50
			,'width' => 50
			,'price' => $this->getRequest()->getDeclaredValueInHryvnias()
			,'weight' => $this->getRequest()->getWeightInKilogrammes()
			,'start_city' => mb_strtoupper($this->getRequest()->getOriginCity())
			,'stop_city' => mb_strtoupper($this->getRequest()->getDestinationCity())
			,'s1' => 1
			,'s2' => 2
		);
	}

	const _CLASS = __CLASS__;
}