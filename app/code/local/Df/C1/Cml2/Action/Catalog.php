<?php
namespace Df\C1\Cml2\Action;
abstract class Df_C1_Cml2_Action_Catalog extends Df_C1_Cml2_Action_GenericImport {
	/** @return Df_C1_Cml2_Import_Data_Document_Catalog */
	protected function getDocumentCurrentAsCatalog() {
		return $this->getFileCurrent()->getXmlDocumentAsCatalog();
	}

	/** @return Df_C1_Cml2_Import_Data_Document_Offers */
	protected function getDocumentCurrentAsOffers() {
		return $this->getFileCurrent()->getXmlDocumentAsOffers();
	}
}