<?php
namespace Df\C1\Cml2\Import\Data\Entity\AttributeValue;
use Df\C1\Cml2\Import\Data\Entity\Attribute\Text;
use Df\C1\Cml2\Import\Data\Entity\Offer;
class Barcode extends \Df\C1\Cml2\Import\Data\Entity\AttributeValue\OfferPart {
	/**
	 * @override
	 * @see \Df\Xml\Parser\Entity::e()
	 * @return \Df\Xml\X
	 */
	public function e() {return $this->getOffer()->e();}

	/**
	 * @override
	 * @return bool
	 */
	public function isValidForImport() {return !!$this->getValueForDataflow();}

	/**
	 * 2015-02-06
	 * @used-by \Df\C1\Cml2\Import\Processor\Product\Type::getProductDataNewOrUpdateAttributeValues()
	 * Метод @used-by Df_Dataflow_Model_Import_Abstract_Row::getFieldValue()
	 * проверяет принадлежность результата @see getValueForDataflow()
	 * одному из типов: string|int|float|bool|null
	 * @override
	 * @return string|int|float|bool|null
	 */
	public function getValueForDataflow() {return $this->getOffer()->leaf('Штрихкод');}

	/**
	 * @override
	 * @return \Df_Catalog_Model_Resource_Eav_Attribute|null
	 */
	protected function findMagentoAttributeInRegistry() {return
		df_attributes()->findByCode($this->getAttributeCodeNew())
	;}

	/**
	 * @override
	 * @return string
	 */
	protected function getAttributeCodeNew() {return 'rm_1c__barcode';}

	/**
	 * @override
	 * @return string
	 */
	protected function getAttributeExternalId() {return 'Штрихкод';}

	/**
	 * @override
	 * @return string
	 */
	protected function getAttributeFrontendLabel() {return 'Штрихкод';}

	/**
	 * @override
	 * @return \Df\C1\Cml2\Import\Data\Entity\Attribute
	 */
	protected function getAttributeTemplate() {return new Text;}

	/** @return int */
	protected function isAttributeVisibleOnFront() {return 0;}

	/**
	 * @param Offer $offer
	 * @return self
	 */
	public static function i(Offer $offer) {return new self([self::P__OFFER => $offer]);}
}


 