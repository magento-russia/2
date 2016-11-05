<?php
namespace Df\C1\Config\Api;
class Product extends \Df\C1\Config\Api\Cml2 {
	/** @return \Df\C1\Config\Api\Product\Description */
	public function description() {return \Df\C1\Config\Api\Product\Description::s();}
	/** @return \Df\C1\Config\Api\Product\Name */
	public function name() {return \Df\C1\Config\Api\Product\Name::s();}
	/** @return \Df\C1\Config\Api\Product\Other */
	public function other() {return \Df\C1\Config\Api\Product\Other::s();}
	/** @return \Df\C1\Config\Api\Product\Prices */
	public function prices() {return \Df\C1\Config\Api\Product\Prices::s();}
	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}