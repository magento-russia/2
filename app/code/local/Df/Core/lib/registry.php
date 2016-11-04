<?php
/**
 * 2015-08-10
 * @used-by \Df\C1\Cml2\Export\Processor\Catalog\Attribute\Real::getИд()
 * @used-by \Df\C1\Cml2\Import\Data\Entity\AttributeValue::getAttributeMagento()
 * @used-by \Df\C1\Cml2\Import\Data\Entity\AttributeValue::createMagentoAttribute()
 * @used-by \Df\C1\Cml2\Import\Data\Entity\AttributeValue\Barcode::findMagentoAttributeInRegistry()
 * @used-by \Df\C1\Cml2\Import\Data\Entity\AttributeValue\ProcurementDate::findMagentoAttributeInRegistry()
 * @used-by \Df\C1\Cml2\Import\Data\Entity\OfferPart\OptionValue::getAttributeMagento()
 * @used-by \Df\C1\Cml2\Import\Data\Entity\OfferPart\OptionValue::setupAttribute()
 * @used-by \Df\C1\Cml2\Import\Data\Entity\OfferPart\OptionValue::getAttributeData()
 * @used-by \Df\C1\Cml2\Import\Data\Entity\ProductPart\AttributeValue\Custom::findMagentoAttributeInRegistry()
 * @used-by \Df\C1\Cml2\Import\Data\Entity\ProductPart\AttributeValue\Custom::isAttributeExistAndBelongToTheProductType()
 * @used-by \Df\C1\Cml2\Import\Data\Entity\ProductPart\AttributeValue\Custom\Option::getAttributeMagento()
 * @used-by \Df\C1\Cml2\Import\Data\Entity\ProductPart\AttributeValue\Custom\Option::createMagentoAttribute()
 * @used-by \Df\C1\Cml2\Import\Data\Entity\ProductPart\AttributeValue\Custom\Option::findMagentoAttributeInRegistry()
 * @used-by \Df\C1\Cml2\Import\Processor\ReferenceList::process()
 * @used-by Df_Catalog_Model_Resource_Eav_Attribute::addOptions()
 * @used-by Df_Dataflow_Model_Importer_Product::getAttributeForField()
 * @used-by Df_Eav_Model_Entity_Attribute_Set::getAttributes()
 * @used-by Df_Eav_Model_Entity_Attribute_Set::addExternalIdAttribute()
 * @used-by Df_Localization_Onetime_Type_Attribute::getAllEntities()
 * @used-by \Df\Shipping\Processor\AddDimensionsToProductAttributeSet::getAttribute()
 * @used-by \Df\YandexMarket\Setup\AttributeSet::getAttributeAdministrative()
 * @used-by Lamoda_Catalog_Setup_Shoes::addAttributeSimple()
 * @used-by Lamoda_Catalog_Setup_Shoes::addReferenceList()
 * @return Df_Dataflow_Model_Registry_Collection_Attributes
 */
function df_attributes() {return Df_Dataflow_Model_Registry_Collection_Attributes::s();}

