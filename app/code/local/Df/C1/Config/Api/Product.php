<?php
namespace Df\C1\Config\Api;
class Product extends \Df\C1\Config\Api\Cml2 {
	/** @return Product\Description */
	public function description() {return Product\Description::s();}
	/** @return Product\Name */
	public function name() {return Product\Name::s();}
	/** @return Product\Other */
	public function other() {return Product\Other::s();}
	/** @return Product\Prices */
	public function prices() {return Product\Prices::s();}
	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}