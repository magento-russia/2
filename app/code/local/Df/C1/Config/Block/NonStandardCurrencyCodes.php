<?php
namespace Df\C1\Config\Block;
class NonStandardCurrencyCodes extends \Df_Admin_Block_Field_DynamicTable {
	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->addColumn(\Df\C1\Config\MapItem\CurrencyCode::P__NON_STANDARD, array(
			'label' => 'код', 'style' => 'width:3em'
		));
		/** @noinspection PhpParamsInspection */
		$this->addColumnRm(\Df_Admin_Config_DynamicTable_Column_Select::i(
			\Df\C1\Config\MapItem\CurrencyCode::P__STANDARD
			, 'валюта'
			, df_options_to_map(\Mage::app()->getLocale()->getOptionCurrencies())
			, array()
			, array('width' => 220)
		));
		$this->_addAfter = false;
		$this->_addButtonLabel = 'добавить...';
	}
}