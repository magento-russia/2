<?php
class Df_C1_Cml2_Import_Data_Entity_PriceType extends Df_C1_Cml2_Import_Data_Entity {
	/**
	 * @todo Надо учитывать НДС
	 * @param float $originalPrice
	 * @return float
	 */
	public function convertPriceToBase($originalPrice) {
		df_param_float($originalPrice, 0);
		return df_currency_h()->convertToBase($originalPrice, $this->getCurrencyCode());
	}

	/** @return string */
	public function getCurrencyCode() {
		return df_c1_currency_code_to_magento_format($this->leafSne('Валюта'));
	}

	/** @return Df_Customer_Model_Group|null */
	public function getCustomerGroup() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_n_set($this->getConfigPrices()->getCustomerGroup($this->getName()));
		}
		return df_n_get($this->{__METHOD__});
	}

	/** @return int|null */
	public function getCustomerGroupId() {
		return $this->getCustomerGroup() ? $this->getCustomerGroup()->getId() : null;
	}

	/**
		<Налог>
			<Наименование>НДС</Наименование>
			<УчтеноВСумме>true</УчтеноВСумме>
		</Налог>
	 * @return bool
	 */
	public function isVatIncluded() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = 'true' ===
				dfa($this->e()->map('Налог', 'Наименование', 'УчтеноВСумме'), 'НДС')
			;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_C1_Config_Api_Product_Prices */
	private function getConfigPrices() {return df_c1_cfg()->product()->prices();}

	/** @used-by Df_C1_Cml2_Import_Data_Collection_PriceTypes::itemClass() */

}