<?php
namespace Df\C1\Cml2\Action;
abstract class Df_C1_Cml2_Action_GenericImport extends Df_C1_Cml2_Action {
	/** @return Df_C1_Cml2_Import_Data_Document */
	protected function getDocumentCurrent() {return $this->state()->getDocumentCurrent();}

	/** @return Df_C1_Cml2_File */
	protected function getFileCurrent() {return $this->state()->getFileCurrent();}

	/** @return Df_C1_Cml2_State_Import */
	private function state() {return Df_C1_Cml2_State_Import::s();}
}