<?php
class Df_Dataflow_Model_Settings extends Df_Core_Model_Settings {
	/** @return Df_Dataflow_Model_Settings_Common */
	public function common() {return Df_Dataflow_Model_Settings_Common::s();}
	/** @return Df_Dataflow_Model_Settings_Patches */
	public function patches() {return Df_Dataflow_Model_Settings_Patches::s();}
	/** @return Df_Dataflow_Model_Settings_Products */
	public function products() {return Df_Dataflow_Model_Settings_Products::s();}
	/** @return Df_Dataflow_Model_Settings */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}