<?php
namespace Df\C1\Cml2\State;
use Df\C1\Cml2\State\Export\Products;
class Export extends \Df_Core_Model {
	/** @return Products */
	public function getProducts() {return Products::s();}

	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}