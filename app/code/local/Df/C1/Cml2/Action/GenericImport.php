<?php
namespace Df\C1\Cml2\Action;
abstract class GenericImport extends \Df\C1\Cml2\Action {
	/** @return \Df\C1\Cml2\Import\Data\Document */
	protected function getDocumentCurrent() {return $this->state()->getDocumentCurrent();}

	/** @return \Df\C1\Cml2\File */
	protected function getFileCurrent() {return $this->state()->getFileCurrent();}

	/** @return \Df\C1\Cml2\State\Import */
	private function state() {return \Df\C1\Cml2\State\Import::s();}
}