<?php
class Df_PonyExpress_Model_Method extends Df_Shipping_Model_Method_CollectedManually {
	/**
	 * @override
	 * @used-by Df_Shipping_Model_Method::_getCost()
	 * @return float
	 */
	protected function getCost() {return rm_float(df_a($this->getVariant(), 'tariffvat'));}

	/**
	 * @override
	 * @used-by Df_Shipping_Model_Method::_getDeliveryTime()
	 * @return int|int[]
	 */
	protected function getDeliveryTime() {
		/** @var string|int() $time */
		$time = df_a($this->getVariant(), 'delivery');
		return is_array($time) ? $time : explode(' - ', $time);
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getTitleBase() {return df_a($this->getVariant(), 'servise');}

	/** @return array(string => string) */
	private function getVariant() {return $this->_getData(self::P__VARIANT);}

	/** @used-by Df_PonyExpress_Model_Collector::createMethodFromVariant() */
	const _C = __CLASS__;
	/** @used-by Df_PonyExpress_Model_Collector::createMethodFromVariant() */
	const P__VARIANT = 'variant';
}