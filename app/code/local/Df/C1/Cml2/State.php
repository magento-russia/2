<?php
namespace Df\C1\Cml2;
class State {
	/** @return \Df\C1\Cml2\State\Export */
	public function export() {return \Df\C1\Cml2\State\Export::s();}

	/** @return \Df\C1\Cml2\Import\Data\Collection\PriceTypes */
	public function getPriceTypes() {return \Df\C1\Cml2\Import\Data\Collection\PriceTypes::s();}

	/** @return \Df\C1\Cml2\State\Import */
	public function import() {return \Df\C1\Cml2\State\Import::s();}

	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}