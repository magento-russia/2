<?php
namespace Df\Shipping;
class Settings extends \Df_Core_Model_Settings {
	/** @return Settings\Message */
	public function message() {return Settings\Message::s();}
	/** @return Settings\Product */
	public function product() {return Settings\Product::s();}
	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}