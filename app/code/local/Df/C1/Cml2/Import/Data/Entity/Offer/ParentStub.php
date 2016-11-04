<?php
namespace Df\C1\Cml2\Import\Data\Entity\Offer;
class ParentStub extends \Df\C1\Cml2\Import\Data\Entity\Offer {
	/**
	 * @override
	 * @return string
	 */
	public function getExternalId() {return $this->getPrototype()->getExternalIdForConfigurableParent();}

	/**
	 * @override
	 * @return string
	 */
	public function getName() {return $this->getEntityProduct()->getName();}

	/** @return \Df\C1\Cml2\Import\Data\Entity\Offer */
	private function getPrototype() {return $this->cfg(self::$P__PROTOTYPE);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__PROTOTYPE, \Df\C1\Cml2\Import\Data\Entity\Offer::class);
	}
	/** @var string */
	private static $P__PROTOTYPE = 'prototype';
	/**
	 * @static
	 * @param \Df\C1\Cml2\Import\Data\Entity\Offer $prototype
	 * @return \Df\C1\Cml2\Import\Data\Entity\Offer\ParentStub
	 */
	public static function i(\Df\C1\Cml2\Import\Data\Entity\Offer $prototype) {
		return new self(array(self::$P__PROTOTYPE => $prototype, self::$P__E => $prototype->e()));
	}
}