<?php
class Df_Ems_Model_Method extends Df_Shipping_Model_Method {
	/**
	 * @override
	 * @return float
	 */
	public function getCost() {
		return $this->processMoney($this->getApi()->getRate());
	}

	/**
	 * @override
	 * @return string
	 */
	public function getMethod() {
		return 'standard';
	}

	/**
	 * @override
	 * @return string
	 */
	public function getMethodTitle() {
		/** @var string $result */
		$result = '';
		if (!is_null($this->getRequest()) && (0 !== $this->getApi()->getTimeOfDeliveryMin())) {
			$result =
				$this->formatTimeOfDelivery(
					$this->getApi()->getTimeOfDeliveryMin()
					,$this->getApi()->getTimeOfDeliveryMax()
				)
			;
		}
		df_result_string($result);
		return $result;
	}

	/** @return Df_Ems_Model_Api_GetConditions */
	private function getApi() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Ems_Model_Api_GetConditions::i(array(
				Df_Ems_Model_Api_GetConditions::P__SOURCE => $this->getPostingSource()
				,Df_Ems_Model_Api_GetConditions::P__DESTINATION =>
					$this->getPostingDestination()
				,Df_Ems_Model_Api_GetConditions::P__WEIGHT => $this->getPostingWeight()
				,Df_Ems_Model_Api_GetConditions::P__POSTING_TYPE => 'att'
			));
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getPostingDestination() {
		return $this->getRequest()->getLocatorDestination()->getResult();
	}

	/** @return string */
	private function getPostingSource() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->getRequest()->getLocatorOrigin()->getResult();
		}
		return $this->{__METHOD__};
	}

	/** @return float */
	private function getPostingWeight() {return $this->getRequest()->getWeightInKilogrammes();}

	/**
	 * @param float $amount
	 * @return float
	 */
	private function processMoney($amount) {
		df_param_float($amount, 0);
		return Mage::app()->getStore()->roundPrice($this->convertFromRoublesToBase($amount));
	}

	const _CLASS = __CLASS__;
}