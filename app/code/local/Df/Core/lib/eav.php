<?php
/**
 * 2015-08-10
 * @param Mage_Eav_Model_Entity_Attribute $attribute
 * @return array(string => string)
 */
function df_attribute_options(Mage_Eav_Model_Entity_Attribute $attribute) {
	return df_eav_cache_ram(null, __FUNCTION__, array($attribute->getAttributeCode() => $attribute));
}

/**
 * 2015-08-10
 * @param Mage_Eav_Model_Entity_Attribute $attribute
 * @return array(string => string)
 */
function df_attribute_options_(Mage_Eav_Model_Entity_Attribute $attribute) {
	df_assert($attribute->usesSource());
	/**
	 * Как ни странно, хотя базовый интерфейс определяет
	 * @see Mage_Eav_Model_Entity_Attribute_Source_Interface::getAllOptions()
	 * как метод без параметров,
	 * класс @see Mage_Core_Model_Design_Source_Design::getAllOptions()
	 * переопределяет этот метод как метод с параметром,
	 * причём значением по умолчанию является true.
	 * Не уверен, что вызов getAllOptions(false) — это лучшее решение, но так делает стандартный код.
	 */
	return $attribute->getSource()->getAllOptions(false);
}

/**
 * 2015-08-10
 * @return Df_Eav_Model_Resource_Entity_Attribute_Set
 */
function df_attribute_set() {
	return Df_Eav_Model_Resource_Entity_Attribute_Set::s();
}

/**
 * 2015-08-10
 * Эта функция значительно упрощает двуступенчатое кэширование.
 * @used-by Df_Eav_Model_Resource_Entity_Attribute_Set::attributeCodes()
 * @used-by Df_Eav_Model_Resource_Entity_Attribute_Set::mapFromNameToId()
 * @used-by Df_Catalog_Model_Resource_Product_Flat_Indexer::getAttributeCodes()
 * @param object|null $object
 * @param string $function
 * @param string|string[]|null|array(string => mixed) $params [optional]
 * @param bool $complex [optional]
 * @param bool $ramOnly [optional]
 * @return mixed|false
 */
function df_eav_cache($object, $function, $params = null, $complex = false, $ramOnly = false) {
	return Df_Eav_Model_Cache::s()->p($object, $function, $params, $complex, $ramOnly);
}

/**
 * 2015-08-11
 * Кэширование только в оперативной памяти.
 * Позволяет избежать дисковых операций по чтению-записи постоянного кэша.
 * @used-by df_attribute_options()
 * @param object|null $object
 * @param string $function
 * @param string|string[]|null|array(string => mixed) $params [optional]
 * @param bool $complex [optional]
 * @return mixed|false
 */
function df_eav_cache_ram($object, $function, $params = null, $complex = false) {
	return Df_Eav_Model_Cache::s()->p($object, $function, $params, $complex, $ramOnly = true);
}

/**
 * Обновляет глобальный кэш EAV.
 * Это нужно, например, при добавлении новых свойств к прикладным типам товаров.
 * @param bool $reindexFlatProducts [optional]
 * @param bool $reindexFlatCategories [optional]
 * @return void
 */
function df_eav_reset($reindexFlatProducts = true, $reindexFlatCategories = false) {
	if (!df_h()->eav()->isPacketUpdate()) {
		Mage::unregister('_singleton/eav/config');
		Df_Eav_Model_Cache::s()->clean();
		if ($reindexFlatProducts) {
			Df_Catalog_Model_Product::reindexFlat();
		}
		if ($reindexFlatCategories) {
			Df_Catalog_Model_Category::reindexFlat();
		}
	}
}

/**
 * @uses df_eav_reset()
 * @return void
 */
function df_eav_reset_categories() {
	df_eav_reset($reindexFlatProducts = false, $reindexFlatCategories = true);
}

/** @return int */
function df_eav_id_product() {
	static $r; return $r ? $r : $r = Df_Eav_Model_Entity::product()->getTypeId();
}

/**
 * 2015-03-13
 * Обратите внимание, что метод @uses Mage_Eav_Model_Entity_Setup::removeAttribute()
 * сам проверяет, присутствует ли свойство, и выполняет работу только при наличии свойства,
 * поэтому вручную проверять присутствие свойства не нужно.
 * @used-by df_remove_category_attribute()
 * @used-by df_remove_product_attribute()
 * @used-by Df_C1_Setup_1_0_2::add1CIdToEntity()
 * $entityTypeId может быть как символьным идентификатором (например, «catalog_product») ,
 * так и числовым (например, «3»):
 * @see Mage_Eav_Model_Entity_Setup::getEntityType()
		return $this->getTableRow('eav/entity_type',
			is_numeric($id) ? 'entity_type_id' : 'entity_type_code', $id, $field
		);
 * @param int|string $entityTypeId
 * @param string $attributeCode
 * @return void
 */
function df_remove_attribute($entityTypeId, $attributeCode) {
	Df_Catalog_Model_Resource_Installer_Attribute::s()->removeAttribute($entityTypeId, $attributeCode);
}

/**
 * @used-by Df_Parser_Setup_2_22_8::_process()
 * @used-by Df_Parser_Setup_2_40_0::_process()
 * @param string $code
 * @return void
 */
function df_remove_category_attribute($code) {df_remove_attribute('catalog_category', $code);}

/**
 * @used-by \Df\C1\Cml2\Import\Data\Entity\ProductPart\AttributeValue\Custom\Option\Manufacturer::findMagentoAttributeInRegistry()
 * @param string $code
 * @return void
 */
function df_remove_product_attribute($code) {df_remove_attribute('catalog_product', $code);}


