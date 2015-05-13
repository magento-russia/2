<?php
class Df_PonyExpress_Model_Method extends Df_Shipping_Model_Method_CollectedManually {
	/**          
	 * @override
	 * @return float
	 */
	public function getCost() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				$this->convertFromRoublesToBase(rm_float(df_a($this->getVariant(), 'tariffvat')))
			;
		}
		return $this->{__METHOD__};
	}
	
	/** @return int */
	protected function getTimeOfDeliveryMax() {return rm_last($this->getTimeOfDeliveryAsArray());}

	/** @return int */
	protected function getTimeOfDeliveryMin() {
		return rm_first($this->getTimeOfDeliveryAsArray());
	}	
	
	/**
	 * @override
	 * @return string
	 */
	protected function getTitleBase() {return df_a($this->getVariant(), 'servise');}
	
	/** @return int[] */
	private function getTimeOfDeliveryAsArray() {
		if (!isset($this->{__METHOD__})) {
			/** @var string|array() $timeOfDelivery */
			$timeOfDelivery = df_a($this->getVariant(), 'delivery');
			$this->{__METHOD__} =
				is_array($timeOfDelivery)
				? $timeOfDelivery
				: rm_int(explode(' - ', $timeOfDelivery))
			;
		}
		return $this->{__METHOD__};
	}

	/** @return array(string => string) */
	private function getVariant() {return $this->_getData(self::P__VARIANT);}

	const _CLASS = __CLASS__;
	const P__VARIANT = 'variant';
}