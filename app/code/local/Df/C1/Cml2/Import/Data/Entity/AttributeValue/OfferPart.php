<?php
namespace Df\C1\Cml2\Import\Data\Entity\AttributeValue;
abstract class OfferPart extends \Df\C1\Cml2\Import\Data\Entity\AttributeValue {
	/**
	 * @override
	 * @return \Df\C1\Cml2\Import\Data\Entity\Product
	 */
	protected function getProduct() {return $this->getOffer()->getEntityProduct();}

	/** @return \Df\C1\Cml2\Import\Data\Entity\Offer */
	protected function getOffer() {return $this->cfg(self::P__OFFER);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__OFFER, \Df\C1\Cml2\Import\Data\Entity\Offer::class);
	}
	const P__OFFER = 'offer';
}


 