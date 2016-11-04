<?php
class Df_C1_Config_Block_NonStandardCurrencyCodes extends Df_Admin_Block_Field_DynamicTable {
	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->addColumn(Df_C1_Config_MapItem_CurrencyCode::P__NON_STANDARD, array(
			'label' => 'код', 'style' => 'width:3em'
		));
		/** @noinspection PhpParamsInspection */
		$this->addColumnRm(Df_Admin_Config_DynamicTable_Column_Select::i(
			Df_C1_Config_MapItem_CurrencyCode::P__STANDARD
			, 'валюта'
			, df_options_to_map(Mage::app()->getLocale()->getOptionCurrencies())
			, array()
			, array('width' => 220)
		));
		$this->_addAfter = false;
		$this->_addButtonLabel = 'добавить...';
	}
}