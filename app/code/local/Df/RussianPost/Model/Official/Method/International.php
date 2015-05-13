<?php
class Df_RussianPost_Model_Official_Method_International extends Df_Shipping_Model_Method {
	/**
	 * @override
	 * @return float
	 */
	public function getCost() {return $this->getApi()->getRate();}

	/**
	 * @override
	 * @return string
	 */
	public function getMethod() {return 'international';}

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
					->checkCountryDestinationIsNotRussia()
					->checkCountryOriginIsRussia()
					->checkWeightIsLE(31.5)
					->checkCountryDestinationIsNotEmpty()
				;
			}
			catch(Exception $e) {
				if ($this->needDisplayDiagnosticMessages()) {throw $e;} else {$result = false;}
			}
		}
		return $result;
	}
	
	/** @return Df_RussianPost_Model_Official_Request_International */
	private function getApi() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_RussianPost_Model_Official_Request_International::i(array(
				Df_RussianPost_Model_Official_Request_International::P__DESTINATION_COUNTRY =>
					$this->getRequest()->getDestinationCountry()
				,Df_RussianPost_Model_Official_Request_International::P__DECLARED_VALUE =>
					rm_round($this->getRequest()->getDeclaredValueInRoubles())
				,Df_RussianPost_Model_Official_Request_International::P__WEIGHT_IN_GRAMMES =>
					rm_round($this->getRequest()->getWeightInGrammes())
			));
		}
		return $this->{__METHOD__};
	}

	const _CLASS = __CLASS__;
}