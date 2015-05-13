<?php
abstract class Df_1C_Model_Cml2_Action_GenericImport extends Df_1C_Model_Cml2_Action {
	/** @return Df_1C_Model_Cml2_Import_Data_Document */
	protected function getDocumentCurrent() {return $this->state()->getDocumentCurrent();}

	/** @return Df_1C_Model_Cml2_File */
	protected function getFileCurrent() {return $this->state()->getFileCurrent();}

	/** @return Df_1C_Model_Cml2_State_Import */
	private function state() {return Df_1C_Model_Cml2_State_Import::s();}
}