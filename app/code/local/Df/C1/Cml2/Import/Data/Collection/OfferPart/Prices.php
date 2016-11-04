<?php
namespace Df\C1\Cml2\Import\Data\Collection\OfferPart;
use Df\C1\Cml2\Import\Data\Entity\Offer;
use Df\C1\Cml2\Import\Data\Entity\OfferPart\Price;
class Prices extends \Df\C1\Cml2\Import\Data\Collection {
	/** @return Price|null */
	public function getMain() {return
		$this->findByExternalId($this->getState()->getPriceTypes()->getMain()->getExternalId())
	;}

	/**
	 * @override
	 * @see \Df\Xml\Parser\Collection::itemClass()
	 * @return string
	 */
	protected function itemClass() {return Price::class;}

	/**
	 * @override
	 * @see \Df\Xml\Parser\Collection::itemPath()
	 * @return string|string[]
	 */
	protected function itemPath() {return 'Цены/Цена';}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__OFFER, Offer::class);
	}
	/** @var string */
	private static $P__OFFER = 'offer';
	/**
	 * @used-by \Df\C1\Cml2\Import\Data\Entity\Offer::getPrices()
	 * @param \Df\Xml\X $e
	 * @param Offer $offer
	 * @return self
	 */
	public static function i(\Df\Xml\X $e, Offer $offer) {return new self([
		self::$P__E => $e, self::$P__OFFER => $offer
	]);}
}