<?php
class Df_RussianPost_Model_Official_Method_International extends Df_Shipping_Model_Method_Russia {
	/**
	 * @override
	 * @return string
	 */
	public function getMethod() {return 'international';}

	/**
	 * @override
	 * @return void
	 * @throws Exception
	 */
	protected function checkApplicability() {
		parent::checkApplicability();
		$this
			->checkCountryDestinationIsNotRussia()
			->checkCountryOriginIsRussia()
			->checkWeightIsLE(31.5)
			->checkCountryDestinationIsNotEmpty()
		;
	}

	/**
	 * @override
	 * @used-by Df_Shipping_Model_Method::_getCost()
	 * @return float
	 */
	protected function getCost() {return $this->getApi()->getRate();}
	
	/** @return Df_RussianPost_Model_Official_Request_International */
	private function getApi() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_RussianPost_Model_Official_Request_International::i(
				$this->rr()->getDestinationCountry()
				,rm_round($this->rr()->getWeightInGrammes())
				,rm_round($this->rr()->getDeclaredValueInRoubles())
			);
		}
		return $this->{__METHOD__};
	}

	/** @used-by Df_RussianPost_Model_Collector::getMethods() */
	const _C = __CLASS__;
}