<?php
namespace Df\C1;
class Cml2 extends \Df_Core_Model {
	/** @return \Df\C1\Cml2\Import\Data\Document */
	protected function getDocumentCurrent() {return $this->getFileCurrent()->getXmlDocument();}

	/** @return \Df\C1\Cml2\Import\Data\Document\Offers */
	protected function getDocumentCurrentAsOffers() {
		return $this->getFileCurrent()->getXmlDocumentAsOffers();
	}

	/** @return \Df\C1\Cml2\File */
	protected function getFileCurrent() {return \Df\C1\Cml2\State\Import::s()->getFileCurrent();}

	/**
	 * Данный метод никак не связан данным с классом,
	 * однако включён в класс для удобного доступа объектов класса к реестру
	 * (чтобы писать $this->getState() вместо \Df\C1\Cml2\State::s())
	 * @return \Df\C1\Cml2\State
	 */
	protected function getState() {return \Df\C1\Cml2\State::s();}
}