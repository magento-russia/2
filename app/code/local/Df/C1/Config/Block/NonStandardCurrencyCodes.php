<?php
namespace Df\C1\Config\Block;
use Df_Admin_Config_DynamicTable_Column_Select as Select;
use Df\C1\Config\MapItem\CurrencyCode as CC;
class NonStandardCurrencyCodes extends \Df_Admin_Block_Field_DynamicTable {
	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->addColumn(CC::P__NON_STANDARD, ['label' => 'код', 'style' => 'width:3em']);
		/** @noinspection PhpParamsInspection */
		$this->addColumnRm(Select::i(
			CC::P__STANDARD
			, 'валюта'
			, df_options_to_map(\Mage::app()->getLocale()->getOptionCurrencies())
			, []
			, ['width' => 220]
		));
		$this->_addAfter = false;
		$this->_addButtonLabel = 'добавить...';
	}
}