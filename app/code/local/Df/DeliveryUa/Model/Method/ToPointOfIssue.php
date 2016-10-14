<?php
class Df_DeliveryUa_Model_Method_ToPointOfIssue extends Df_DeliveryUa_Model_Method {
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
		if ($this->configS()->needGetCargoFromTheShopStore()) {
			$this->checkWeightIsLE(30.0);
		}
		else {
			$this->checkWeightIsGT(30.0);
		}
	}

	/**
	 * @override
	 * @return bool
	 */
	protected function needDeliverToHome() {return false;}
}