<?php
class Df_Sales_Model_Settings_OrderGrid extends Df_Core_Model_Settings {
	/** @return Df_Sales_Model_Settings_OrderGrid_ProductColumn */
	public function productColumn() {return Df_Sales_Model_Settings_OrderGrid_ProductColumn::s();}
	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}