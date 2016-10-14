<?php
class Df_Core_Model_Store_Group extends Mage_Core_Model_Store_Group {
	/**
	 * @override
	 * @return Df_Core_Model_Resource_Store_Group_Collection
	 */
	public function getResourceCollection() {return self::c();}

	/**
	 * @override
	 * @return Df_Core_Model_Resource_Store_Group
	 * 2016-10-14
	 * В родительском классе метод переобъявлен через PHPDoc,
	 * и поэтому среда разработки думает, что он публичен.
	 */
	/** @noinspection PhpHierarchyChecksInspection */
	protected function _getResource() {return Df_Core_Model_Resource_Store_Group::s();}

	/** @used-by Df_Core_Model_Resource_Store_Group_Collection::_construct() */
	const _C = __CLASS__;
	/**
	 * @static
	 * @param bool $loadDefault [optional]
	 * @return Df_Core_Model_Resource_Store_Group_Collection
	 */
	public static function c($loadDefault = false) {
		/** @var Df_Core_Model_Resource_Store_Group_Collection $result */
		$result = new Df_Core_Model_Resource_Store_Group_Collection;
		$result->setLoadDefault($loadDefault);
		return $result;
	}
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Core_Model_Store_Group
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
	/** @return Df_Core_Model_Store_Group */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}