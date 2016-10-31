<?php
class Df_1C_Config_Api_ReferenceLists extends Df_1C_Config_Api_Cml2 {
	/** @return string */
	public function updateMode() {return $this->v('df_1c/reference_lists/update_mode');}
	/** @return Df_1C_Config_Api_ReferenceLists */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}