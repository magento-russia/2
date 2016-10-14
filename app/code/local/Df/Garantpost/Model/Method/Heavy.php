<?php
abstract class Df_Garantpost_Model_Method_Heavy extends Df_Garantpost_Model_Method {
	/**
	 * @abstract
	 * @return string
	 */
	abstract protected function getLocationDestinationSuffix();

	/**
	 * @override
	 * @return void
	 * @throws Exception
	 */
	protected function checkApplicability() {
		parent::checkApplicability();
		$this
			->checkCountryDestinationIs(Df_Directory_Helper_Country::ISO_2_CODE__RUSSIA)
			->checkWeightIsGT(31.5)
			->checkWeightIsLE(80)
		;
		if (!$this->getCostInRoubles()) {
			$this->throwExceptionNoRate();
		}
	}

	/**
	 * @override
	 * @used-by Df_Shipping_Model_Method::_getCost()
	 * @return int
	 */
	protected function getCost() {return rm_nat($this->getApiRate()->getResult());}

	/** @return Df_Garantpost_Model_Request_Rate_Heavy */
	private function getApiRate() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Garantpost_Model_Request_Rate_Heavy::i(array(
				Df_Garantpost_Model_Request_Rate_Heavy::P__WEIGHT => $this->rr()->getWeightInKilogrammes()
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
		return implode(' ', array($this->rr()->getDestinationCity(), $this->getLocationDestinationSuffix()));
	}

	/** @return string|null */
	private function getLocationOriginId() {
		return
			$this->isDeliveryFromMoscow()
			? 'msk'
			: (df_strings_are_equal_ci('Московская', $this->rr()->getOriginRegionName())
				? 'obl'
				: null
			)
		;
	}

	/** @return string */
	private function getServiceCode() {
		/** @var array(bool => string) $states */
		$states = array(false => 'term', true => 'door');
		return implode('-', array(
			df_a($states, $this->configS()->needDeliverCargoToTheBuyerHome())
			,df_a($states, $this->configS()->needGetCargoFromTheShopStore())
		));
	}
}