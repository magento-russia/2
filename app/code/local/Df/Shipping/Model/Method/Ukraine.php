<?php
abstract class Df_Shipping_Model_Method_Ukraine extends Df_Shipping_Model_Method {
	/** @return float */
	abstract protected function getCostInHryvnias();
	
	/**
	 * @override
	 * @return float
	 */
	public function getCost() {
		if (!isset($this->{__METHOD__})) {
			if (0 === $this->getConstInHryvniasCached()) {
				$this->throwExceptionCalculateFailure();
			}
			/** @var float $result */
			$this->{__METHOD__} =
				rm_currency()->convertFromHryvniasToBase($this->getConstInHryvniasCached())
			;
		}
		return $this->{__METHOD__};
	}
	
	/** @return float */
	private function getConstInHryvniasCached() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->getCostInHryvnias();
		}
		return $this->{__METHOD__};
	}
}