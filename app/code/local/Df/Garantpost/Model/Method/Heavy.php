<?php
abstract class Df_Garantpost_Model_Method_Heavy extends Df_Garantpost_Model_Method {
	/**
	 * @abstract
	 * @return string
	 */
	abstract protected function getLocationDestinationSuffix();

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
					->checkCountryDestinationIs(Df_Directory_Helper_Country::ISO_2_CODE__RUSSIA)
					->checkWeightIsGT(31.5)
					->checkWeightIsLE(80)
				;
				if (0 >= $this->getCostInRoubles()) {
					$this->throwExceptionNoRate();
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
	protected function getCostInRoubles() {return rm_nat($this->getApiRate()->getResult());}

	/** @return Df_Garantpost_Model_Request_Rate_Heavy */
	private function getApiRate() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Garantpost_Model_Request_Rate_Heavy::i(array(
				Df_Garantpost_Model_Request_Rate_Heavy::P__WEIGHT =>
					$this->getRequest()->getWeightInKilogrammes()
				,Df_Garantpost_Model_Request_Rate_Heavy::P__SERVICE => $this->getServiceCode()
				,Df_Garantpost_Model_Request_Rate_Heavy::P__LOCATION_ORIGIN_ID =>
					$this->getLocationOriginId()
				,Df_Garantpost_Model_Request_Rate_Heavy::P__LOCATION_DESTINATION_NAME =>
					$this->getLocationDestinationName()
			));
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getLocationDestinationName() {
		$this->checkCityDestinationIsNotEmpty();
		/** @var string $result */
		$result =
			implode(
				' '
				,array(
					$this->getRequest()->getDestinationCity()
					,$this->getLocationDestinationSuffix()
				)
			)
		;
		return $result;
	}

	/** @return string|null */
	private function getLocationOriginId() {
		/** @var string|null $result */
		$result = null;
		if ($this->isDeliveryFromMoscow()) {
			$result = 'msk';
		}
		else {
			if (
				df_strings_are_equal_ci(
					'Московская'
					,$this->getRequest()->getOriginRegionName()
				)
			) {
				$result = 'obl';
			}
		}
		if (!is_null($result)) {
			df_result_string($result);
		}
		return $result;
	}

	/** @return string */
	private function getServiceCode() {
		/** @var string[] $states */
		$states =
			array(
				false => 'term'
				,true => 'door'
			)
		;
		return
			implode(
				'-'
				,array(
					df_a(
						$states
						,$this->getRmConfig()->service()->needDeliverCargoToTheBuyerHome()
					)
					,df_a(
						$states
						,$this->getRmConfig()->service()->needGetCargoFromTheShopStore()
					)
				)
			)
		;
	}

	const _CLASS = __CLASS__;
}