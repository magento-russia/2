<?php
class Df_Ems_Model_Method extends Df_Shipping_Model_Method {
	/**
	 * @override
	 * @used-by Df_Shipping_Model_Method::_getCost()
	 * @return float
	 */
	protected function getCost() {return $this->processMoney($this->getApi()->getRate());}

	/**
	 * @override
	 * @used-by Df_Shipping_Model_Method::_getDeliveryTime()
	 * @return int|int[]
	 */
	protected function getDeliveryTime() {
		return array($this->getApi()->getDeliveryTimeMin(), $this->getApi()->getDeliveryTimeMax());
	}

	/** @return Df_Ems_Model_Api_GetConditions */
	private function getApi() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Ems_Model_Api_GetConditions::i(
				$this->getPostingSource()
				,$this->getPostingDestination()
				,$this->getPostingWeight()
				, 'att'
			);
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getPostingDestination() {return $this->rr()->getLocatorDestination()->getResult();}

	/** @return string */
	private function getPostingSource() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->rr()->getLocatorOrigin()->getResult();
		}
		return $this->{__METHOD__};
	}

	/** @return float */
	private function getPostingWeight() {return $this->rr()->getWeightInKilogrammes();}

	/**
	 * @param float $amount
	 * @return float
	 */
	private function processMoney($amount) {
		df_param_float($amount, 0);
		return rm_store()->roundPrice($this->convertFromRoublesToBase($amount));
	}
}