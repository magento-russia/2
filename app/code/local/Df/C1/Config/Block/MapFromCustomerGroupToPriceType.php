<?php
class Df_C1_Config_Block_MapFromCustomerGroupToPriceType extends Df_Admin_Block_Field_DynamicTable {
	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->addColumnRm(Df_Admin_Config_DynamicTable_Column_Select::i(
			Df_C1_Config_MapItem_PriceType::P__CUSTOMER_GROUP
			, 'категория покупателей'
			, Df_Customer_Model_Group::c()->setRealGroupsFilter()->toOptionHash()
		));
		$this->addColumn(Df_C1_Config_MapItem_PriceType::P__PRICE_TYPE, array(
			'label' => 'типовое соглашение / вид цен'
			,'style' => 'width:15em'
		));
		$this->_addAfter = false;
		$this->_addButtonLabel = 'добавить...';
	}
}