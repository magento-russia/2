<?php
class Df_C1_Cml2 extends Df_Core_Model {
	/** @return Df_C1_Cml2_Import_Data_Document */
	protected function getDocumentCurrent() {return $this->getFileCurrent()->getXmlDocument();}

	/** @return Df_C1_Cml2_Import_Data_Document_Offers */
	protected function getDocumentCurrentAsOffers() {
		return $this->getFileCurrent()->getXmlDocumentAsOffers();
	}

	/** @return Df_C1_Cml2_File */
	protected function getFileCurrent() {return Df_C1_Cml2_State_Import::s()->getFileCurrent();}

	/**
	 * Данный метод никак не связан данным с классом,
	 * однако включён в класс для удобного доступа объектов класса к реестру
	 * (чтобы писать $this->getState() вместо Df_C1_Cml2_State::s())
	 * @return Df_C1_Cml2_State
	 */
	protected function getState() {return Df_C1_Cml2_State::s();}
}