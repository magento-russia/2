<?php
namespace Df\C1\Cml2\Import\Data\Entity\OfferPart\OptionValue;
use Df\C1\Cml2\Import\Data\Entity\Offer;
use Df_Catalog_Model_Resource_Eav_Attribute as Attribute;
class EmptyT extends \Df\C1\Cml2\Import\Data\Entity\OfferPart\OptionValue {
	/**
	 * @override
	 * @return Attribute
	 */
	public function getAttributeMagento() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->_getAttributeMagento();
			// Нельзя объединять это выражение с предыдущим,
			// чтобы не попасть в рекурсию.
			$this->setupAttribute($this->{__METHOD__});
			$this->setupOption($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return string
	 */
	public function getName() {return $this->_getAttributeMagento()->getFrontendLabel();}

	/**
	 * @override
	 * @return string
	 */
	public function getValue() {return self::$VALUE__UNKNOWN;}

	/**
	 * Этот метод необходим, иначе @used-by getName() приведёт к сбою.
	 * @return Attribute
	 */
	private function _getAttributeMagento() {return $this->cfg(self::$P__ATTRIBUTE);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__ATTRIBUTE, Attribute::class);
	}
	/** @var string */
	private static $P__ATTRIBUTE = 'attribute';
	/**
	 * @param Offer $offer
	 * @param Attribute $attribute
	 * @return self
	 */
	public static function i2(Offer $offer, Attribute $attribute) {return new self([
		self::P__OFFER => $offer, self::$P__ATTRIBUTE => $attribute
	]);}
}