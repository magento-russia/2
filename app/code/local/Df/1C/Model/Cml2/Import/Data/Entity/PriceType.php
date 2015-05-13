<?php
class Df_1C_Model_Cml2_Import_Data_Entity_PriceType extends Df_1C_Model_Cml2_Import_Data_Entity {
	/**
	 * @todo Надо учитывать НДС
	 * @param float $originalPrice
	 * @return float
	 */
	public function convertPriceToBase($originalPrice) {
		df_param_float($originalPrice, 0);
		return rm_currency()->convertToBase($originalPrice, $this->getCurrencyCode());
	}

	/** @return string */
	public function getCurrencyCode() {
		return df_h()->_1c()->cml2()->convertCurrencyCodeToMagentoFormat($this->getEntityParam('Валюта'));
	}
	
	/** @return Mage_Customer_Model_Group|null */
	public function getCustomerGroup() {
		return
			df_a(
				df_cfg()->_1c()->product()->prices()->getMapFromPriceTypeNameToCustomerGroup()
				,$this->getName()
			)
		;
	}

	/** @return int|null */
	public function getCustomerGroupId() {
		return
			df_a(
				df_cfg()->_1c()->product()->prices()->getMapFromPriceTypeNameToCustomerGroupId()
				,$this->getName()
			)
		;
	}

	/** @return bool */
	public function isVatIncluded() {
		if (!isset($this->{__METHOD__})) {
			/** @var bool $result */
			$result = false;
			/** @var string[] $taxSettings */
			$taxSettings = $this->getEntityParamArray('Налог');
			df_assert_array($taxSettings);
			/** @var string $taxName */
			$taxName = df_a($taxSettings, 'Наименование');
			df_assert_string($taxName);
			if ('НДС' === $taxName) {
				/** @var string $isIncluded */
				$isIncluded = df_a($taxSettings, 'УчтеноВСумме');
				df_assert_string($isIncluded);
				$result = ('true' === $isIncluded);
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * Используется из @see Df_1C_Model_Cml2_Import_Data_Collection_PriceTypes::getItemClass()
	 */
	const _CLASS = __CLASS__;
}