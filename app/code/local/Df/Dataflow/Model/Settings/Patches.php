<?php
class Df_Dataflow_Model_Settings_Patches extends Df_Core_Model_Settings {
	/** @return boolean */
	public function fixFieldMappingGui() {return $this->getYesNo('df_dataflow/patches/fix_field_mapping_gui');}
	/** @return Df_Dataflow_Model_Settings_Patches */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}