<?php
class Df_Rating_Model_Rating_Entity extends Mage_Rating_Model_Rating_Entity {
	/** @return int */
	public function getIdForProductRating() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_nat($this->getIdByCode('product'));
		}
		return $this->{__METHOD__};
	}
	
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Rating_Model_Rating_Entity
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
	/** @return Df_Rating_Model_Rating_Entity */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}