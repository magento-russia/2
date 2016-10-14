<?php
/** @return Df_1C_Helper_Data */
function rm_1c() {return Df_1C_Helper_Data::s();}

/**
 * Добавляет к прикладному типу товаров свойство «внешний идентификатор 1С».
 * Все требуемые для такого добавления операции выполняются только при необходимости
 * (свойство добавляется, только если оно ещё не было добавлено ранее).
 * @param Df_Eav_Model_Entity_Attribute_Set $attributeSet
 * @return void
 */
function rm_1c_add_external_id_attribute_to_set(Df_Eav_Model_Entity_Attribute_Set $attributeSet) {
	$attributeSet->addExternalIdAttribute(
		Df_1C_Const::ENTITY_EXTERNAL_ID
		, 'Идентификатор товара в 1С'
		, Df_1C_Const::PRODUCT_ATTRIBUTE_GROUP_NAME
		, 2
	);
}

/** @return Df_1C_Config_Api */
function rm_1c_cfg() {return Df_1C_Config_Api::s();}

/**
 * Пример внешнего идентификатора: «6cc37c6d-7d15-11df-901f-00e04c595000».
 * @used-by Df_1C_Cml2_Import_Data_Collection_ProductPart_AttributeValues_Custom::createItem()
 * @used-by Df_1C_Cml2_Import_Data_Entity_ProductPart_AttributeValue_Custom_Option::getExternalId()
 * @param $string|null
 * @return bool
 */
function rm_1c_is_external_id($string) {
	return is_string($string) && 36 === mb_strlen($string) && 5 === count(explode('-', $string));
}

/**
 * @param string|mixed[] $arguments
 * @return void
 */
function rm_1c_log($arguments) {
	/** @uses func_get_args() не может быть параметром другой функции */
	$arguments = is_array($arguments) ? $arguments : func_get_args();
	rm_1c()->log(rm_format($arguments));
}

/**
 * @param Df_Catalog_Model_Product $product
 * @return void
 */
function rm_1c_reindex_product(Df_Catalog_Model_Product $product) {
	$product
		->reindexPrices()
		->reindexStockStatus()
		->reindexUrlRewrites()
	;
}