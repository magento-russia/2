<?php
class Df_Garantpost_Model_Method_Export extends Df_Garantpost_Model_Method {
	/**
	 * @override
	 * @return string
	 */
	public function getMethod() {return self::METHOD;}

	/**
	 * @override
	 * @return string
	 */
	public function getMethodTitle() {
		/** @var string $result */
		$result = '';
		if (!is_null($this->getRequest())) {
			/** @var string|null $forCapital */
			$forCapital =
				(0 === $this->getApiDeliveryTime()->getCapitalMin())
				? null
				: rm_sprintf(
					'столица: %s'
					,$this->formatTimeOfDelivery(
						$this->getApiDeliveryTime()->getCapitalMin()
						,$this->getApiDeliveryTime()->getCapitalMax()
					)
				)
			;
			/** @var string|null $forNonCapital */
			$forNonCapital =
				(0 === $this->getApiDeliveryTime()->getNonCapitalMin())
				? null
				: rm_sprintf(
					is_null($forCapital) ? '%s' : 'другие города: %s'
					,$this->formatTimeOfDelivery(
						$this->getApiDeliveryTime()->getNonCapitalMin()
						,$this->getApiDeliveryTime()->getNonCapitalMax()
					)
				)
			;
			$result = rm_concat_clean(' ' ,$forCapital, $forNonCapital);
		}
		return $result;
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
					->checkCountryDestinationIsNot(Df_Directory_Helper_Country::ISO_2_CODE__RUSSIA)
					->checkWeightIsLE(32)
				;
				if (
					is_null(
						df_a(
							Df_Garantpost_Model_Request_Countries_ForRate::s()->getResponseAsArray()
							,$this->getRequest()->getDestinationCountryId()
						)
					)
				) {
					$this->throwExceptionInvalidDestinationCountry();
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
	 * @return int
	 */
	protected function getCostInRoubles() {return rm_nat0($this->getApiRate()->getResult());}

	/** @return Df_Garantpost_Model_Request_DeliveryTime_Export */
	private function getApiDeliveryTime() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Garantpost_Model_Request_DeliveryTime_Export::i(
				$this->getRequest()->getDestinationCountryId()
			);
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Garantpost_Model_Request_Rate_Export */
	private function getApiRate() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				Df_Garantpost_Model_Request_Rate_Export::i(
					$this->getRequest()->getDestinationCountryId()
					, $this->getRequest()->getWeightInKilogrammes()
				)
			;
		}
		return $this->{__METHOD__};
	}

	const _CLASS = __CLASS__;
	const METHOD = 'export';
}