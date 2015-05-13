<?php
abstract class Df_1C_Model_Cml2_Action_Catalog extends Df_1C_Model_Cml2_Action_GenericImport {
	/** @return Df_1C_Model_Cml2_Import_Data_Document_Catalog */
	protected function getDocumentCurrentAsCatalog() {
		return $this->getFileCurrent()->getXmlDocumentAsCatalog();
	}

	/** @return Df_1C_Model_Cml2_Import_Data_Document_Offers */
	protected function getDocumentCurrentAsOffers() {
		return $this->getFileCurrent()->getXmlDocumentAsOffers();
	}
}