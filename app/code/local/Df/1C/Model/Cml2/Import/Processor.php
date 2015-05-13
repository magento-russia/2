<?php
abstract class Df_1C_Model_Cml2_Import_Processor extends Df_1C_Model_Cml2 {
	/**
	 * @abstract
	 * @return void
	 */
	abstract public function process();

	/** @return Df_1C_Model_Cml2_Import_Data_Entity */
	protected function getEntity() {return $this->cfg(self::P__ENTITY);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__ENTITY, Df_1C_Model_Cml2_Import_Data_Entity::_CLASS);
	}
	const _CLASS = __CLASS__;
	const P__ENTITY = 'entity';
}