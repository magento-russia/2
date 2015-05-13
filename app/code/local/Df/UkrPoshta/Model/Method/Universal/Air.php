<?php
class Df_UkrPoshta_Model_Method_Universal_Air extends Df_UkrPoshta_Model_Method_Universal {
	/**
	 * @override
	 * @return string
	 */
	public function getMethod() {
		return 'post-air';
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getTransportType() {
		return 'Air';
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
				/**
				 * Авиадоставка доступна только для зарубежных отправлений
				 * @link http://services.ukrposhta.com/CalcUtil/PostalMails.aspx
				 */
				$this
					->checkCountryDestinationIsNot(Df_Directory_Helper_Country::ISO_2_CODE__UKRAINE)
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
		return false;
	}

	const _CLASS = __CLASS__;

}