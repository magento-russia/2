<?php
abstract class Df_Pec_Model_Method extends Df_Shipping_Model_Method_CollectedManually {
	/**
	 * @override
	 * @return void
	 * @throws Exception
	 */
	protected function checkApplicability() {
		parent::checkApplicability();
		$this->checkCityDestinationIsNotEmpty();
	}
}