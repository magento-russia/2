<?php
class Df_Pickup_Block_Admin_Point_Index_Grid extends Df_Core_Block_Admin_Entity_Index_Grid {
	/** @return Df_Pickup_Model_Resource_Point_Collection */
	public function getCollection() {return parent::getCollection();}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->setDefaultSort(Df_Pickup_Model_Point::P__NAME);
		$this->setDefaultDir('ASC');
	}

	/**
	 * @override
	 * @return Df_Pickup_Block_Admin_Point_Index_Grid
	 */
	protected function _prepareCollection() {
		$this->getCollection()->joinLocation();
		parent::_prepareCollection();
		return $this;
	}

	/**
	 * @override
	 * @return Mage_Adminhtml_Block_Widget_Grid
	 */
	protected function _prepareColumns() {
		$this
			->addColumn(
				Df_Pickup_Model_Point::P__NAME
				,array(
					'header' => 'название'
					,'index' => Df_Pickup_Model_Point::P__NAME
				)
			)
			->addColumn(
				Df_Core_Model_Location::P__CITY
				,array(
					'header' => 'город'
					,'index' => Df_Core_Model_Location::P__CITY
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
		return Df_Pickup_Model_Resource_Point_Collection::_CLASS;
	}
	const _CLASS = __CLASS__;
}