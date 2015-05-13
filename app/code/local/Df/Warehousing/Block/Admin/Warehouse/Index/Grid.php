<?php
class Df_Warehousing_Block_Admin_Warehouse_Index_Grid extends Df_Core_Block_Admin_Entity_Index_Grid {
	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->setDefaultSort(Df_Warehousing_Model_Warehouse::P__NAME);
		$this->setDefaultDir('ASC');
	}

	/**
	 * @override
	 * @return Mage_Adminhtml_Block_Widget_Grid
	 */
	protected function _prepareColumns() {
		$this
			->addColumn(
				Df_Warehousing_Model_Warehouse::P__NAME
				,array(
					'header' => 'название'
					,'index' => Df_Warehousing_Model_Warehouse::P__NAME
				)
			)
		;
		return parent::_prepareColumns();
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getCollectionClass() {
		return Df_Warehousing_Model_Resource_Warehouse_Collection::_CLASS;
	}

	const _CLASS = __CLASS__;
}