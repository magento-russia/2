<?php
class Df_1C_Model_Cml2_State {
	/** @return Df_1C_Model_Cml2_State_Export */
	public function export() {return Df_1C_Model_Cml2_State_Export::s();}

	/** @return Df_1C_Model_Cml2_Import_Data_Collection_PriceTypes */
	public function getPriceTypes() {return Df_1C_Model_Cml2_Import_Data_Collection_PriceTypes::s();}

	/** @return Df_1C_Model_Cml2_State_Import */
	public function import() {return Df_1C_Model_Cml2_State_Import::s();}

	/** @return Df_1C_Model_Cml2_State */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}