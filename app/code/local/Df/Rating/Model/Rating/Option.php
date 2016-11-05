<?php
class Df_Rating_Model_Rating_Option extends Mage_Rating_Model_Rating_Option {
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Rating_Model_Rating_Option
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}