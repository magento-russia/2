<?php
namespace Df\C1\Cml2;
class State {
	/** @return State\Export */
	public function export() {return State\Export::s();}

	/** @return \Df\C1\Cml2\Import\Data\Collection\PriceTypes */
	public function getPriceTypes() {return \Df\C1\Cml2\Import\Data\Collection\PriceTypes::s();}

	/** @return State\Import */
	public function import() {return State\Import::s();}

	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}