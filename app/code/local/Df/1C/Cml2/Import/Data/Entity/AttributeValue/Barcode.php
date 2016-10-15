<?php
class Df_1C_Cml2_Import_Data_Entity_AttributeValue_Barcode
	extends Df_1C_Cml2_Import_Data_Entity_AttributeValue_OfferPart {
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
	 * @used-by Df_1C_Cml2_Import_Processor_Product_Type::getProductDataNewOrUpdateAttributeValues()
	 * Метод @used-by Df_Dataflow_Model_Import_Abstract_Row::getFieldValue()
	 * проверяет принадлежность результата @see getValueForDataflow()
	 * одному из типов: string|int|float|bool|null
	 * @override
	 * @return string|int|float|bool|null
	 */
	public function getValueForDataflow() {return $this->getOffer()->leaf('Штрихкод');}

	/**
	 * @override
	 * @return Df_Catalog_Model_Resource_Eav_Attribute|null
	 */
	protected function findMagentoAttributeInRegistry() {
		return rm_attributes()->findByCode($this->getAttributeCodeNew());
	}

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
	 * @return Df_1C_Cml2_Import_Data_Entity_Attribute
	 */
	protected function getAttributeTemplate() {
		return new Df_1C_Cml2_Import_Data_Entity_Attribute_Text();
	}

	/** @return int */
	protected function isAttributeVisibleOnFront() {return 0;}

	/**
	 * @param Df_1C_Cml2_Import_Data_Entity_Offer $offer
	 * @return Df_1C_Cml2_Import_Data_Entity_AttributeValue_Barcode
	 */
	public static function i(Df_1C_Cml2_Import_Data_Entity_Offer $offer) {
		return new self(array(self::P__OFFER => $offer));
	}
}


 