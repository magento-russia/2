<?php
class Df_1C_Model_Settings_ReferenceLists extends Df_1C_Model_Settings_Cml2 {
	/** @return string */
	public function updateMode() {return $this->getString('df_1c/reference_lists/update_mode');}
	/** @return Df_1C_Model_Settings_ReferenceLists */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}