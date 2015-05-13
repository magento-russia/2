<?php
class Df_1C_Model_Cml2_Import_Data_Entity_Order extends Df_1C_Model_Cml2_Import_Data_Entity {
	/** @return string */
	public function getIncrementId() {return $this->getEntityParam('Номер');}

	/** @return Df_1C_Model_Cml2_Import_Data_Collection_Order_Items */
	public function getItems() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				Df_1C_Model_Cml2_Import_Data_Collection_Order_Items::i($this->e(), $this)
			;
		}
		return $this->{__METHOD__};
	}
	
	/** @return Df_Sales_Model_Order|null */
	public function getOrder() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_n_set(
				Df_Sales_Model_Order::ldi($this->getIncrementId(), $throwOnError = false)
			);
		}
		return rm_n_get($this->{__METHOD__});
	}

	/** Используется из @see Df_1C_Model_Cml2_Import_Data_Collection_Orders::getItemClass() */
	const _CLASS = __CLASS__;
}