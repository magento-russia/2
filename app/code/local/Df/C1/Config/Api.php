<?php
namespace Df\C1\Config;
class Api extends \Df_Core_Model_Settings {
	/** @return \Df\C1\Config\Api\CatalogExport */
	public function catalogExport() {return \Df\C1\Config\Api\CatalogExport::s();}
	/** @return \Df\C1\Config\Api\General */
	public function general() {return \Df\C1\Config\Api\General::s();}
	/** @return \Df\C1\Config\Api\Orders */
	public function orders() {return \Df\C1\Config\Api\Orders::s();}
	/** @return \Df\C1\Config\Api\Product */
	public function product() {return \Df\C1\Config\Api\Product::s();}
	/** @return \Df\C1\Config\Api\ReferenceLists */
	public function referenceLists() {return \Df\C1\Config\Api\ReferenceLists::s();}
	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}