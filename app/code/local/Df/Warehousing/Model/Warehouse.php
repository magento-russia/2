<?php
class Df_Warehousing_Model_Warehouse extends Df_Core_Model_Entity {
	/** @return Df_Core_Model_Location */
	public function getLocation() {return $this->getDependencyByName(self::DEPENDENCY__LOCATION);}

	/** @return int */
	public function getLocationId() {return $this->cfg(self::P__LOCATION_ID);}

	/**
	 * @override
	 * @return string
	 */
	public function getName() {return $this->cfg(self::P__NAME);}

	/**
	 * @override
	 * @return Df_Core_Model_Entity
	 */
	protected function initDependenciesInfo() {
		parent::initDependenciesInfo();
		$this->getDependenciesInfo()->addDependency(
			$name = self::DEPENDENCY__LOCATION
			,$className = Df_Core_Model_Form_Location::_CLASS
			,$actionSaveClassName = Df_Core_Model_Action_Location_Save::_CLASS
			,$idFieldName = self::P__LOCATION_ID
			,$deleteCascade = true
		);
		return $this;
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_init(Df_Warehousing_Model_Resource_Warehouse::mf());
		$this->_prop(self::P__LOCATION_ID, self::V_NAT0);
	}
	const _CLASS = __CLASS__;
	const DEPENDENCY__LOCATION = 'location';
	const P__ID = 'warehouse_id';
	const P__LOCATION_ID = 'location_id';
	const P__NAME = 'name';
	/**
	 * @see Df_Warehousing_Model_Resource_Warehouse_Collection::_construct()
	 * @return string
	 */
	public static function mf() {static $r; return $r ? $r : $r = rm_class_mf(__CLASS__);}
}