<?php
class Df_Garantpost_Model_Method_Export extends Df_Garantpost_Model_Method {
	/**
	 * @override
	 * @return string
	 */
	public function getMethod() {return 'export';}

	/**
	 * @override
	 * @return void
	 * @throws Exception
	 */
	protected function checkApplicability() {
		parent::checkApplicability();
		$this
			->checkCountryDestinationIsNot(Df_Directory_Helper_Country::ISO_2_CODE__RUSSIA)
			->checkWeightIsLE(32)
		;
		if (is_null(df_a(
			Df_Garantpost_Model_Request_Countries_ForRate::s()->getResponseAsArray()
			,$this->rr()->getDestinationCountryId()
		))) {
			$this->throwExceptionInvalidCountryDestination();
		}
	}

	/**
	 * @override
	 * @used-by Df_Shipping_Model_Method::_getCost()
	 * @return int
	 */
	protected function getCost() {return rm_nat0($this->apiRate()->getResult());}

	/**
	 * @override
	 * @used-by Df_Shipping_Model_Method::_getDeliveryTime()
	 * @return int|int[]
	 */
	protected function getDeliveryTime() {
		/** @var Df_Garantpost_Model_Request_DeliveryTime_Export $t */
		$t = Df_Garantpost_Model_Request_DeliveryTime_Export::i($this->rr()->getDestinationCountryId());
		return array(
			max($t->getCapitalMin(), $t->getNonCapitalMin())
			, max($t->getCapitalMax(), $t->getNonCapitalMax())
		);
	}

	/** @return Df_Garantpost_Model_Request_Rate_Export */
	private function apiRate() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Garantpost_Model_Request_Rate_Export::i(
				$this->rr()->getDestinationCountryId()
				, $this->rr()->getWeightInKilogrammes()
			);
		}
		return $this->{__METHOD__};
	}
}