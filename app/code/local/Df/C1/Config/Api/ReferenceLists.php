<?php
namespace Df\C1\Config\Api;
class ReferenceLists extends \Df\C1\Config\Api\Cml2 {
	/** @return string */
	public function updateMode() {return $this->v('df_1c/reference_lists/update_mode');}
	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}