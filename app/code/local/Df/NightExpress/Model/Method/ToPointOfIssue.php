<?php
class Df_NightExpress_Model_Method_ToPointOfIssue extends Df_NightExpress_Model_Method {
	/**
	 * @override
	 * @return string
	 */
	public function getMethod() {
		return 'to-point-of-issue';
	}

	/**
	 * @override
	 * @return bool
	 * @throws Exception
	 */
	public function isApplicable() {
		/** @var bool $result */
		$result = true;
		if ($result) {
			try {
				$this
					->checkCountryOriginIsUkraine()
					->checkCountryDestinationIsUkraine()
				;
				if ($this->getRmConfig()->service()->needGetCargoFromTheShopStore()) {
					$this->checkWeightIsLE(30);
				}
				else {
					$this->checkWeightIsGT(30);
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
	 * @return bool
	 */
	protected function needDeliverToHome() {
		return false;
	}

	const _CLASS = __CLASS__;
}