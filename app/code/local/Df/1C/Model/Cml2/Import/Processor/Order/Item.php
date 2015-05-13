<?php
abstract class Df_1C_Model_Cml2_Import_Processor_Order_Item extends Df_1C_Model_Cml2_Import_Processor {
	/**
	 * @override
	 * @return Df_1C_Model_Cml2_Import_Data_Entity_Order_Item
	 */
	protected function getEntity() {return parent::getEntity();}

	/** @return Df_1C_Model_Cml2_Import_Data_Entity_Order */
	protected function getEntityOrder() {return $this->cfg(self::P__ENTITY_ORDER);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__ENTITY, Df_1C_Model_Cml2_Import_Data_Entity_Order_Item::_CLASS)
			->_prop(self::P__ENTITY_ORDER, Df_1C_Model_Cml2_Import_Data_Entity_Order::_CLASS)
		;
	}

	const P__ENTITY_ORDER = 'entity_order';
	const _CLASS = __CLASS__;
}