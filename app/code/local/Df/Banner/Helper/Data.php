<?php
class Df_Banner_Helper_Data extends Mage_Core_Helper_Abstract {
	/** @return Df_Banner_Helper_Image */
	public function image() {return Df_Banner_Helper_Image::s();}
	/** @return Df_Banner_Helper_Image2 */
	public function image2() {return Df_Banner_Helper_Image2::s();}
	/** @return Df_Banner_Helper_Data */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}