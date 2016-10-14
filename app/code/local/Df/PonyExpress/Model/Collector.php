<?php
class Df_PonyExpress_Model_Collector extends Df_Shipping_Collector {
	/**
	 * @override
	 * @return Df_PonyExpress_Model_Method[]
	 */
	protected function getMethods() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_PonyExpress_Model_Method[] $result */
			$result = array();
			foreach ($this->getApi()->getVariants() as $variant) {
				/** @var array(string => string) $variant */
				$result[]= $this->createMethodFromVariant($variant);
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param array(string => string) $variant
	 * @return Df_PonyExpress_Model_Method
	 */
	private function createMethodFromVariant(array $variant) {
		/** @var Df_PonyExpress_Model_Method $result */
		$result = $this->createMethod(Df_PonyExpress_Model_Method::_C, df_a($variant, 'servise'));
		$result->setData(Df_PonyExpress_Model_Method::P__VARIANT, $variant);
		return $result;
	}

	/** @return Df_PonyExpress_Model_Request_Rate */
	private function getApi() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_PonyExpress_Model_Request_Rate::i(
				$this->getRateRequest(), $this->main()
			);
		}
		return $this->{__METHOD__};
	}
}