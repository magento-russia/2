<?php
class Df_Megapolis_Model_Location extends Df_Shipping_Model_Location {
	/**
	 * @override
	 * @return int
	 */
	public function getId() {return $this->cfg(self::P__ID);}

	/** @return string */
	public function getName() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->normalizeName($this->cfg(self::P__NAME));
		}
		return $this->{__METHOD__};
	}
	
	/**
	 * @override
	 * @return string
	 */
	public function getRegion() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->normalizeRegionName($this->cfg(self::P__REGION));
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return array(string => string)
	 */
	protected function getRegionReplacementMap() {
		return array_merge(
			parent::getRegionReplacementMap()
			,array('Северная Осетия-Алания' => 'Северная Осетия — Алания')
		);
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__ID, RM_V_INT);
	}
	const _C = __CLASS__;
	const P__ID = 'id';
	const P__NAME = 'name';
	const P__REGION = 'location';
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Megapolis_Model_Location
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}