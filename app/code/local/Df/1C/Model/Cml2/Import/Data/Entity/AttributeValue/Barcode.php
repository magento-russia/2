<?php
class Df_1C_Model_Cml2_Import_Data_Entity_AttributeValue_Barcode
	extends Df_1C_Model_Cml2_Import_Data_Entity_AttributeValue_OfferPart {
	/**
	 * @override
	 * @return Df_Varien_Simplexml_Element
	 */
	public function getSimpleXmlElement() {return $this->getOffer()->e();}

	/**
	 * @override
	 * @return bool
	 */
	public function isValidForImport() {return !!$this->getValueForDataflow();}

	/**
	 * @override
	 * @return string
	 */
	public function getValueForDataflow() {return $this->getOffer()->getEntityParam('Штрихкод');}

	/**
	 * @override
	 * @return Mage_Catalog_Model_Resource_Eav_Attribute|null
	 */
	protected function findMagentoAttributeInRegistry() {
		return df()->registry()->attributes()->findByCode($this->getAttributeCodeNew());
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
	 * @return Df_1C_Model_Cml2_Import_Data_Entity_Attribute
	 */
	protected function getAttributeTemplate() {
		return new Df_1C_Model_Cml2_Import_Data_Entity_Attribute_Text();
	}

	/** @return int */
	protected function isAttributeVisibleOnFront() {return 0;}

	/**
	 * @param Df_1C_Model_Cml2_Import_Data_Entity_Offer $offer
	 * @return Df_1C_Model_Cml2_Import_Data_Entity_AttributeValue_Barcode
	 */
	public static function i(Df_1C_Model_Cml2_Import_Data_Entity_Offer $offer) {
		return new self(array(self::P__OFFER => $offer));
	}
}


 