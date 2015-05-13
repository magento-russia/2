<?php
class Df_InTime_Model_Method_ToPointOfIssue extends Df_InTime_Model_Method {
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
	 */
	public function isApplicable() {
		/** @var bool $result */
		$result =
				(
						Df_Directory_Helper_Country::ISO_2_CODE__UKRAINE
					===
						$this->getRequest()->getOriginCountryId()
				)
			&&
				(
						Df_Directory_Helper_Country::ISO_2_CODE__UKRAINE
					===
						$this->getRequest()->getDestinationCountryId()
				)
			&&
				(
						$this->getRmConfig()->service()->needGetCargoFromTheShopStore()
					?
						(30.0 >= $this->getRequest()->getWeightInKilogrammes())
					:

						(30.0 < $this->getRequest()->getWeightInKilogrammes())
				)
		;
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