<?php
class Df_Checkout_Block_Frontend_Ergonomic_Address_Field_Country
	extends Df_Checkout_Block_Frontend_Ergonomic_Address_Field_Dropdown {
	/** @return string */
	public function getDropdownAsHtml() {return $this->getDropdownAsBlock()->toHtml();}

	/** @return mixed */
	public function getValue() {
		/** @var mixed $result */
		$result = $this->getAddress()->getAddress()->getCountryId();
		if (is_null($result)) {
			$result =
				/**
				 * Нельзя использовать df_mage()->coreHelper()->getDefaultCountry(),
				 * потому что метод Mage_Core_Helper_Data::getDefaultCountry
				 * отсутствует в Magento 1.4.0.1
				 */
				Mage::getStoreConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_COUNTRY)
			;
		}
		return $result;
	}

	/**
	 * @override
	 * @return string|null
	 */
	protected function getDefaultTemplate() {return self::DEFAULT_TEMPLATE;}

	/** @return Df_Directory_Model_Resource_Country_Collection */
	private function getCountries() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Directory_Model_Country::c();
			$this->{__METHOD__}->loadByStore();
		}
		return $this->{__METHOD__};
	}

	/** @return array */
	private function getCountriesAsOptions() {
		if (!isset($this->{__METHOD__})) {
			/** @var array|null $result */
			$result = null;
			/** @var bool $useCache */
			$useCache  = Mage::app()->useCache('config');
			/** @var string $cacheId */
			$cacheId = 'DIRECTORY_COUNTRY_SELECT_STORE_' . Mage::app()->getStore()->getCode();
			if ($useCache) {
				/** @var string|bool $resultFromCache */
				$resultFromCache = Mage::app()->loadCache($cacheId);
				if ($resultFromCache) {
					$result = unserialize($resultFromCache);
				}
			}
			if (!$result) {
				$result = $this->getCountries()->toOptionArray();
			}
			if ($useCache) {
				Mage::app()->saveCache(serialize($result), $cacheId, array('config'));
			}
			df_result_array($result);
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return Mage_Core_Block_Html_Select */
	private function getDropdownAsBlock() {
		if (!isset($this->{__METHOD__})) {
			/** @var Mage_Core_Block_Html_Select $result */
			$result =
				df_block(
					'core/html_select'
					,null
					,array(
						'name' => $this->getDomName()
						,'id' => $this->getDomId()
						,'title' => $this->getLabel()
						,'class' => 'validate-select'
						,'value' => $this->getValue()
					)
				)
			;
			$result->setOptions($this->getCountriesAsOptions());
			if (
					Df_Checkout_Block_Frontend_Ergonomic_Address::TYPE__SHIPPING
				===
					$this->getAddress()->getType()
			) {
				$result->setData('extra_params', 'onchange="shipping.setSameAsBilling(false);"');
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	const _CLASS = __CLASS__;
	const DEFAULT_TEMPLATE = 'df/checkout/ergonomic/address/field/country.phtml';
}