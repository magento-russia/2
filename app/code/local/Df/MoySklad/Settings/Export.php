<?php
namespace Df\MoySklad\Settings;
// 2016-10-09
class Export extends \Df_Core_Model_Settings {
	/** @return Export\Products */
	public function products() {return Export\Products::s();}
	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}