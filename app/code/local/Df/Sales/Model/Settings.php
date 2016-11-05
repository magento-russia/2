<?php
class Df_Sales_Model_Settings extends Df_Core_Model_Settings {
	/** @return Df_Sales_Model_Settings_OrderComments */
	public function orderComments() {return Df_Sales_Model_Settings_OrderComments::s();}
	/** @return Df_Sales_Model_Settings_OrderGrid */
	public function orderGrid() {return Df_Sales_Model_Settings_OrderGrid::s();}
	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}