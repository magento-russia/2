<?php
class Df_Core_Model_Resource_Layout_Collection extends Df_Core_Model_Resource_Collection {
	/**
	 * @override
	 * @return Df_Core_Model_Resource_Layout
	 */
	public function getResource() {return Df_Core_Model_Resource_Layout::s();}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {$this->_itemObjectClass = Df_Core_Model_Layout_Data::_C;}
	const _C = __CLASS__;
}
 