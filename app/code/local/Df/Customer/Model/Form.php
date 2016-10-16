<?php
class Df_Customer_Model_Form extends Mage_Customer_Model_Form {
	/**
	 * Цель перекрытия —
	 * учёт настроек видимости и обязательности для заполнения полей оформления заказа
	 * модуля «Удобная настройка витрины».
	 * @override
	 * @return Mage_Core_Model_Abstract|bool
	 */
	protected function _getFormAttributeCollection() {
		/** @var Mage_Core_Model_Abstract|bool $result */
		$result = parent::_getFormAttributeCollection();
		if (
			$result instanceof Mage_Customer_Model_Resource_Form_Attribute_Collection
			&& $this->getAddress()) {
			/** @var Mage_Customer_Model_Resource_Form_Attribute_Collection|Mage_Customer_Model_Entity_Form_Attribute_Collection $result */
			$result->setFlag(Df_Customer_Const_Form_Attribute_Collection::P__ADDRESS, $this->getAddress());
		}
		return $result;
	}

	/** @return Mage_Customer_Model_Address_Abstract|null */
	private function getAddress() {
		if (!isset($this->{__METHOD__})) {
			/** @var Mage_Customer_Model_Address_Abstract|null $result */
			$result = null;
			if ($this->getEntity() instanceof Mage_Customer_Model_Address_Abstract) {
				$result = $this->getEntity();
			}
			else if (df_ruri_contains('saveBilling')) {
				$result = df_quote_address_billing();
			}
			else if (df_ruri_contains('saveShipping')) {
				$result = df_quote_address_shipping();
			}
			$this->{__METHOD__} = df_n_set($result);
		}
		return df_n_get($this->{__METHOD__});
	}


}