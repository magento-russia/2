<?php
class Df_Reports_Helper_Data extends Mage_Core_Helper_Abstract {
	/** @return Df_Reports_Helper_GroupResultsByWeek */
	public function groupResultsByWeek() {
		return Df_Reports_Helper_GroupResultsByWeek::s();
	}

	/** @return Df_Reports_Model_Settings */
	public function settings() {
		return Df_Reports_Model_Settings::s();
	}

	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}