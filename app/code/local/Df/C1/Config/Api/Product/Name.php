<?php
namespace Df\C1\Config\Api\Product;
class Name extends \Df\C1\Config\Api\Cml2 {
	/** @return string */
	public function getSource() {return $this->v('df_1c/product__name/source');}
	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}