<?php
namespace Df\C1\Cml2\State;
class Export extends \Df_Core_Model {
	/** @return \Df\C1\Cml2\State\Export\Products */
	public function getProducts() {return \Df\C1\Cml2\State\Export\Products::s();}

	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}