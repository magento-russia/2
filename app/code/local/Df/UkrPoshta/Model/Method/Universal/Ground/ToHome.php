<?php
class Df_UkrPoshta_Model_Method_Universal_Ground_ToHome
	extends Df_UkrPoshta_Model_Method_Universal_Ground {
	/**
	 * @override
	 * @return string
	 */
	public function getMethod() {
		return 'universal-ground-to-home';
	}

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
					->checkCountryDestinationIs(Df_Directory_Helper_Country::ISO_2_CODE__UKRAINE)
				;
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
	public function needDeliverToHome() {
		return true;
	}

	const _CLASS = __CLASS__;

}