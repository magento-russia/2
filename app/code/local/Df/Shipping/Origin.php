<?php
namespace Df\Shipping;
use Df_Directory_Model_Region as Region;
/** @used-by \Df\Shipping\Config\Backend\Validator\Strategy\Origin; */
class Origin extends \Df_Core_Model {
	/** @return string */
	public function getCity() {return $this[self::P__CITY];}

	/** @return \Df_Directory_Model_Country|null */
	public function getCountry() {return dfc($this, function() {return
		!$this->getCountryId() ? null : df_country($this->getCountryId())
	;});}

	/** @return string */
	public function getCountryId() {return $this[self::P__COUNTRY_ID];}

	/** @return string */
	public function getPostalCode() {return $this[self::P__POSTAL_CODE];}

	/** @return Region|null */
	public function getRegion() {return dfc($this, function() {return
		!$this->getRegionId() ? null : Region::ld($this->getRegionId())
	;});}

	/** @return int */
	public function getRegionId() {return $this->cfg(self::P__REGION_ID);}

	/** @return string */
	public function getRegionName() {return
		$this->getRegion() ? $this->getRegion()->getName() : $this[self::P__REGION_NAME]
	;}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__CITY, DF_V_STRING, DF_F_TRIM)
			->_prop(self::P__COUNTRY_ID, DF_V_STRING, false)
			->_prop(self::P__POSTAL_CODE, DF_V_STRING, DF_F_TRIM)
			->_prop(self::P__REGION_NAME, DF_V_STRING, DF_F_TRIM)
			->_prop(self::P__REGION_ID, DF_V_NAT0)
		;
	}

	const P__CITY = 'city';
	const P__COUNTRY_ID = 'country_id';
	const P__POSTAL_CODE = 'postal_code';
	const P__REGION_NAME = 'region_name';
	const P__REGION_ID = 'region_id';
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return self
	 */
	public static function i(array $parameters = []) {return new self($parameters);}
}