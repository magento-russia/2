<?php
namespace Df\C1\Cml2\Import\Data\Entity\Offer;
use Df\C1\Cml2\Import\Data\Entity\Offer;
class ParentStub extends Offer {
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

	/** @return Offer */
	private function getPrototype() {return $this->cfg(self::$P__PROTOTYPE);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__PROTOTYPE, Offer::class);
	}
	/** @var string */
	private static $P__PROTOTYPE = 'prototype';
	/**
	 * @static
	 * @param Offer $prototype
	 * @return self
	 */
	public static function i(Offer $prototype) {return new self([
		self::$P__PROTOTYPE => $prototype, self::$P__E => $prototype->e()
	]);}
}