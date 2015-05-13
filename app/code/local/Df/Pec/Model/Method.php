<?php
abstract class Df_Pec_Model_Method extends Df_Shipping_Model_Method_CollectedManually {
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
				$this->checkCityDestinationIsNotEmpty();
			}
			catch(Exception $e) {
				if ($this->needDisplayDiagnosticMessages()) {throw $e;} else {$result = false;}
			}
		}
		return $result;
	}
	const _CLASS = __CLASS__;
}