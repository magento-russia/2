<?php
namespace Df\C1\Cml2\Import\Data\Collection\OfferPart;
class Prices extends \Df\C1\Cml2\Import\Data\Collection {
	/** @return \Df\C1\Cml2\Import\Data\Entity\OfferPart\Price|null */
	public function getMain() {
		return $this->findByExternalId($this->getState()->getPriceTypes()->getMain()->getExternalId());
	}

	/**
	 * @override
	 * @see \Df\Xml\Parser\Collection::itemClass()
	 * @return string
	 */
	protected function itemClass() {return \Df\C1\Cml2\Import\Data\Entity\OfferPart\Price::class;}

	/**
	 * @override
	 * @see \Df\Xml\Parser\Collection::itemPath()
	 * @return string|string[]
	 */
	protected function itemPath() {return 'Цены/Цена';}

	/** @return \Df\C1\Cml2\Import\Data\Entity\Offer */
	private function getOffer() {return $this->cfg(self::$P__OFFER);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__OFFER, \Df\C1\Cml2\Import\Data\Entity\Offer::class);
	}
	/** @var string */
	private static $P__OFFER = 'offer';
	/**
	 * @used-by \Df\C1\Cml2\Import\Data\Entity\Offer::getPrices()
	 * @static
	 * @param \Df\Xml\X $e
	 * @param \Df\C1\Cml2\Import\Data\Entity\Offer $offer
	 * @return \Df\C1\Cml2\Import\Data\Collection\OfferPart\Prices
	 */
	public static function i(\Df\Xml\X $e, \Df\C1\Cml2\Import\Data\Entity\Offer $offer) {
		return new self(array(self::$P__E => $e, self::$P__OFFER => $offer));
	}
}