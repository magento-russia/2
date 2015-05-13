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
		if (df_h()->customer()->check()->formAttributeCollection($result) && $this->getAddress()) {
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
			else if (rm_contains(Mage::app()->getRequest()->getRequestUri(), 'saveBilling')) {
				$result = rm_session_checkout()->getQuote()->getBillingAddress();
			}
			else if (rm_contains(Mage::app()->getRequest()->getRequestUri(), 'saveShipping')) {
				$result = rm_session_checkout()->getQuote()->getShippingAddress();
			}
			if (!is_null($result)) {
				df_assert($result instanceof Mage_Customer_Model_Address_Abstract);
			}
			$this->{__METHOD__} = rm_n_set($result);
		}
		return rm_n_get($this->{__METHOD__});
	}

	const _CLASS = __CLASS__;
}