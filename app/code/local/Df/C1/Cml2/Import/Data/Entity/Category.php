<?php
namespace Df\C1\Cml2\Import\Data\Entity;
use Df\C1\Cml2\Import\Data\Collection\Categories as C;
class Category extends \Df\C1\Cml2\Import\Data\Entity {
	/** @return C */
	public function getChildren() {return dfc($this, function() {return C::i($this->e());});}
}