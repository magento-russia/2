<?php
namespace Df\C1\Cml2\Import\Data\Entity\AttributeValue;
use Df\C1\Cml2\Import\Data\Entity\Offer;
use Df\C1\Cml2\Import\Data\Entity\Product;
abstract class OfferPart extends \Df\C1\Cml2\Import\Data\Entity\AttributeValue {
	/**
	 * @override
	 * @return Product
	 */
	protected function getProduct() {return $this->getOffer()->getEntityProduct();}

	/** @return Offer */
	protected function getOffer() {return $this->cfg(self::P__OFFER);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__OFFER, Offer::class);
	}
	const P__OFFER = 'offer';
}


 