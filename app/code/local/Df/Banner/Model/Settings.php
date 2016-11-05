<?php
class Df_Banner_Model_Settings extends Df_Core_Model_Settings {
	/** @return boolean */
	public function getEnabled() {return $this->getYesNo('df_promotion/banners/enabled');}
	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}