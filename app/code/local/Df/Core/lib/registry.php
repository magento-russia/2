<?php
/**
 * 2015-08-10
 * @used-by Df_1C_Cml2_Export_Processor_Catalog_Attribute_Real::getИд()
 * @used-by Df_1C_Cml2_Import_Data_Entity_AttributeValue::getAttributeMagento()
 * @used-by Df_1C_Cml2_Import_Data_Entity_AttributeValue::createMagentoAttribute()
 * @used-by Df_1C_Cml2_Import_Data_Entity_AttributeValue_Barcode::findMagentoAttributeInRegistry()
 * @used-by Df_1C_Cml2_Import_Data_Entity_AttributeValue_ProcurementDate::findMagentoAttributeInRegistry()
 * @used-by Df_1C_Cml2_Import_Data_Entity_OfferPart_OptionValue::getAttributeMagento()
 * @used-by Df_1C_Cml2_Import_Data_Entity_OfferPart_OptionValue::setupAttribute()
 * @used-by Df_1C_Cml2_Import_Data_Entity_OfferPart_OptionValue::getAttributeData()
 * @used-by Df_1C_Cml2_Import_Data_Entity_ProductPart_AttributeValue_Custom::findMagentoAttributeInRegistry()
 * @used-by Df_1C_Cml2_Import_Data_Entity_ProductPart_AttributeValue_Custom::isAttributeExistAndBelongToTheProductType()
 * @used-by Df_1C_Cml2_Import_Data_Entity_ProductPart_AttributeValue_Custom_Option::getAttributeMagento()
 * @used-by Df_1C_Cml2_Import_Data_Entity_ProductPart_AttributeValue_Custom_Option::createMagentoAttribute()
 * @used-by Df_1C_Cml2_Import_Data_Entity_ProductPart_AttributeValue_Custom_Option::findMagentoAttributeInRegistry()
 * @used-by Df_1C_Cml2_Import_Processor_ReferenceList::process()
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

