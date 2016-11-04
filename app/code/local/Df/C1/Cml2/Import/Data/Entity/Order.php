<?php
namespace Df\C1\Cml2\Import\Data\Entity;
class Df_C1_Cml2_Import_Data_Entity_Order extends Df_C1_Cml2_Import_Data_Entity {
	/** @return string */
	public function getIncrementId() {return $this->leafSne('Номер');}

	/** @return Df_C1_Cml2_Import_Data_Collection_Order_Items */
	public function getItems() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_C1_Cml2_Import_Data_Collection_Order_Items::i($this->e(), $this);
		}
		return $this->{__METHOD__};
	}
	
	/** @return Df_Sales_Model_Order|null */
	public function getOrder() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_n_set(
				Df_Sales_Model_Order::ldi($this->getIncrementId(), $throwOnError = false)
			);
		}
		return df_n_get($this->{__METHOD__});
	}

	/**
	 * @used-by Df_C1_Cml2_Import_Data_Collection_Orders::itemClass()
	 * @used-by Df_C1_Cml2_Import_Data_Collection_Order_Items::_construct()
	 * @used-by Df_C1_Cml2_Import_Data_Entity_Order_Item::_construct()
	 * @used-by Df_C1_Cml2_Import_Processor_Order::_construct()
	 * @used-by Df_C1_Cml2_Import_Processor_Order_Item::_construct()
	 */

}