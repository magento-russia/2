<?php
class Df_Pickup_Model_Point extends Df_Core_Model_Entity {
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
		$this
			->getDependenciesInfo()->addDependency(
				$name = self::DEPENDENCY__LOCATION
				,$className = Df_Core_Model_Form_Location::_CLASS
				,$actionSaveClassName = Df_Core_Model_Action_Location_Save::_CLASS
				,$idFieldName = self::P__LOCATION_ID
				,$deleteCascade = true
			)
		;
		return $this;
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_init(Df_Pickup_Model_Resource_Point::mf());
		$this->_prop(self::P__LOCATION_ID, self::V_NAT0);
	}
	const _CLASS = __CLASS__;
	const DEPENDENCY__LOCATION = 'location';
	const P__ID = 'point_id';
	const P__LOCATION_ID = 'location_id';
	const P__NAME = 'name';
	/** @return Df_Pickup_Model_Resource_Point_Collection */
	public static function c() {return self::s()->getCollection();}
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Pickup_Model_Point
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
	/**
	 * @see Df_Pickup_Model_Resource_Point_Collection::_construct()
	 * @return string
	 */
	public static function mf() {static $r; return $r ? $r : $r = rm_class_mf(__CLASS__);}
	/** @return Df_Pickup_Model_Point */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}