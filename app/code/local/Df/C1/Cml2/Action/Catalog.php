<?php
namespace Df\C1\Cml2\Action;
abstract class Catalog extends \Df\C1\Cml2\Action\GenericImport {
	/** @return \Df\C1\Cml2\Import\Data\Document\Catalog */
	protected function getDocumentCurrentAsCatalog() {return
		$this->getFileCurrent()->getXmlDocumentAsCatalog()
	;}

	/** @return \Df\C1\Cml2\Import\Data\Document\Offers */
	protected function getDocumentCurrentAsOffers() {return
		$this->getFileCurrent()->getXmlDocumentAsOffers()
	;}
}