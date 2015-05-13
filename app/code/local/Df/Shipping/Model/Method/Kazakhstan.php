<?php
abstract class Df_Shipping_Model_Method_Kazakhstan extends Df_Shipping_Model_Method {
	/** @return float */
	abstract protected function getCostInTenge();
	
	/**
	 * @override
	 * @return float
	 */
	public function getCost() {
		if (!isset($this->{__METHOD__})) {
			if (0 === $this->getCostInTengeCached()) {
				$this->throwExceptionCalculateFailure();
			}
			/** @var float $result */
			$this->{__METHOD__} = rm_currency()->convertFromTengeToBase($this->getCostInTengeCached());
		}
		return $this->{__METHOD__};
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
				$this->checkCountryOriginIsKazakhstan();
			}
			catch(Exception $e) {
				if ($this->needDisplayDiagnosticMessages()) {throw $e;} else {$result = false;}
			}
		}
		return $result;
	}
	
	/** @return float */
	private function getCostInTengeCached() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->getCostInTenge();
		}
		return $this->{__METHOD__};
	}
}