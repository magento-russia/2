<?php
class Df_NightExpress_Model_Method_ToPointOfIssue extends Df_NightExpress_Model_Method {
	/**
	 * @override
	 * @return string
	 */
	public function getMethod() {return 'to-point-of-issue';}

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
		;
		if ($this->configS()->needGetCargoFromTheShopStore()) {
			$this->checkWeightIsLE(30);
		}
		else {
			$this->checkWeightIsGT(30);
		}
	}

	/**
	 * @override
	 * @return bool
	 */
	protected function needDeliverToHome() {return false;}
}