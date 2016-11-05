<?php
namespace Df\C1\Config;
class Api extends \Df_Core_Model_Settings {
	/** @return Api\CatalogExport */
	public function catalogExport() {return Api\CatalogExport::s();}
	/** @return Api\General */
	public function general() {return Api\General::s();}
	/** @return Api\Orders */
	public function orders() {return Api\Orders::s();}
	/** @return Api\Product */
	public function product() {return Api\Product::s();}
	/** @return Api\ReferenceLists */
	public function referenceLists() {return Api\ReferenceLists::s();}
	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}