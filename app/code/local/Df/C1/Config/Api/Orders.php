<?php
namespace Df\C1\Config\Api;
class Orders extends \Df\C1\Config\Api\Cml2 {
	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}