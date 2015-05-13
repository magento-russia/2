<?php
class Df_Shipping_Model_Config_Backend_Validator_Strategy_Origin
	extends Df_Shipping_Model_Config_Backend_Validator_Strategy {
	/**
	 * @override
	 * @return bool
	 */
	public function validate() {return $this->getStrategy()->validate();}

	/** @return Df_Shipping_Model_Origin */
	protected function getOrigin() {
		if (!isset($this->{__METHOD__})) {
			/** @var string|int $region */
			$region = $this->getShippingOriginParam('region_id');
			/** @var bool $hasRegionId */
			$hasRegionId = df_check_integer($region);
			$this->{__METHOD__} = Df_Shipping_Model_Origin::i(array(
				Df_Shipping_Model_Origin::P__CITY => $this->getShippingOriginParam('city')
				,Df_Shipping_Model_Origin::P__COUNTRY_ID =>
					$this->getShippingOriginParam('country_id')
				,Df_Shipping_Model_Origin::P__POSTAL_CODE =>
					$this->getShippingOriginParam('postcode')
				,Df_Shipping_Model_Origin::P__REGION_ID => $hasRegionId ? rm_nat0($region) : null
				,Df_Shipping_Model_Origin::P__REGION_NAME => $hasRegionId ? null : $region
			));
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param string $paramName
	 * @param string $defaultValue[optional]
	 * @return string
	 */
	private function getShippingOriginParam($paramName, $defaultValue = '') {
		/** @var string $result */
		$result = Mage::getStoreConfig(rm_config_key('shipping/origin', $paramName), $this->getStore());
		return $result ? $result : $defaultValue;
	}

	/**
	 * У стратегии тоже есть стратегии
	 * @return Df_Shipping_Model_Config_Backend_Validator_Strategy_Origin
	 */
	private function getStrategy() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_model($this->getStrategyClassMf(), $this->getData());
			df_assert($this->{__METHOD__} instanceof Df_Shipping_Model_Config_Backend_Validator_Strategy_Origin);
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getStrategyClassMf() {
		return $this->getBackend()->getFieldConfigParam('df_origin_validator');
	}

	const _CLASS = __CLASS__;
}