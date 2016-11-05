<?php
/**
 * @method int|null getRegionId()
 * @method Df_Directory_Model_Resource_Region getResource()
 */
class Df_Directory_Model_Region extends Mage_Directory_Model_Region {
	/** @return string|null */
	public function getCapital() {return $this->_getData(self::P__DF_CAPITAL);}

	/** @return string */
	public function getNameOriginal() {
		/** @var string $result */
		$result = $this->getData(self::P__ORIGINAL_NAME);
		return $result ? $result : $this->getName();
	}

	/**
	 * @override
	 * @return Df_Directory_Model_Resource_Region_Collection
	 */
	public function getResourceCollection() {return self::c();}

	/**
	 * @override
	 * @return Df_Directory_Model_Resource_Region
	 * 2016-10-14
	 * В родительском классе метод переобъявлен через PHPDoc,
	 * и поэтому среда разработки думает, что он публичен.
	 */
	/** @noinspection PhpHierarchyChecksInspection */
	protected function _getResource() {return Df_Directory_Model_Resource_Region::s();}

	/** @used-by Df_Directory_Model_Resource_Region_Collection::_construct() */

	const P__COUNTRY_ID = 'country_id';
	const P__CODE = 'code';
	const P__DEFAULT_NAME = 'default_name';
	const P__DF_CAPITAL = 'df_capital';
	const P__DF_TYPE = 'df_type';
	const P__LOCALE = 'locale';
	const P__NAME = 'name';
	const P__ORIGINAL_NAME = 'original_name';
	const P__REGION_ID = 'region_id';

	/** @return Df_Directory_Model_Resource_Region_Collection */
	public static function c() {return new Df_Directory_Model_Resource_Region_Collection;}
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Directory_Model_Region
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
	/**
	 * @static
	 * @param int|string $id
	 * @param string|null $f [optional]
	 * @return Df_Directory_Model_Region
	 */
	public static function ld($id, $f = null) {return dfcf(function($id, $f = null) {return
		df_load(self::i(), $id, $f)
	;}, func_get_args());}
	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}