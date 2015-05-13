<?php
abstract class Df_Core_Block_Admin_Entity_Index_Grid extends Mage_Adminhtml_Block_Widget_Grid {
	/**
	 * @abstract
	 * @return string
	 */
	abstract protected function getCollectionClass();

	/**
	 * @override
	 * @return Varien_Data_Collection
	 */
	public function getCollection() {
		if (!isset($this->_collection)) {
			/** @var string $class */
			$class = $this->getCollectionClass();
			$this->_collection = new $class;
			df_assert($this->_collection instanceof Varien_Data_Collection);
		}
		return $this->_collection;
	}

	/**
	 * @override
	 * @param Df_Core_Model_Abstract $row
	 * @return string
	 */
	public function getRowUrl($row) {
		return $this->getUrl('*/*/edit', array('id' => $row->getId()));
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->setId(get_class($this));
		$this->setData('use_ajax', true);
		$this->setSaveParametersInSession(true);
	}

	const _CLASS = __CLASS__;
}