<?php
class Df_Megapolis_Model_Collector extends Df_Shipping_Model_Collector {
	/**
	 * @override
	 * @return Df_Megapolis_Model_Method[]
	 */
	protected function getMethods() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Megapolis_Model_Method[] $result */
			$result = array();
			/** @var Df_Megapolis_Model_Method $method */
			$method = $this->createMethod(Df_Megapolis_Model_Method::_CLASS, $title = 'Стандартный');
			if ($method->isApplicable()) {
				$method->getCost();
				$result[]= $method;
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	const _CLASS = __CLASS__;
}