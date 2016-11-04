<?php
namespace Df\C1\Cml2\Import\Data\Entity;
class Category extends \Df\C1\Cml2\Import\Data\Entity {
	/** @return \Df\C1\Cml2\Import\Data\Collection\Categories */
	public function getChildren() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = \Df\C1\Cml2\Import\Data\Collection\Categories::i($this->e());
		}
		return $this->{__METHOD__};
	}
}