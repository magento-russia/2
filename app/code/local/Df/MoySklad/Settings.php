<?php
namespace Df\MoySklad;
class Settings extends \Df_Core_Model_Settings {
	/** @return Settings\Export */
	public function export() {return Settings\Export::s();}
	/** @return Settings\General */
	public function general() {return Settings\General::s();}
	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}