<?php
class Df_Directory_Block_Field_CountriesOrdered extends Df_Admin_Block_Field_DynamicTable {
	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		/**
		 * 2015-02-06
		 * Для колонок, требующих вместо стандартного текстового поля ввода
		 * некий другой элемент управления (в данном случае — выпадающий список),
		 * в качестве второго параметра @uses addColumn() передаём не массив,
		 * а объект класса @uses Df_Admin_Config_DynamicTable_Column().
		 * @used-by _renderCellTemplate()
		 */
		$this->addColumnRm(Df_Admin_Config_DynamicTable_Column_Select::i(
			Df_Directory_Config_MapItem_Country::P__ISO2
			, 'страна'
			, rm_countries_options('-- выберите страну --')
			, array()
			, array('width' => 180, 'dropdownCss' => array('width' => 200))
		));
		$this->_addAfter = false;
		$this->_addButtonLabel = 'добавить...';
	}
}