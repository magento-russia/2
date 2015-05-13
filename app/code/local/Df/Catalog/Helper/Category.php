<?php
/**
 * Класс — пустой (не используется)!
 * rewrite — убрал!
 * Класс оставил только ради комментария, к методу @see getStoreCategories()
 * Комментарий важен!
 */
class Df_Catalog_Helper_Category extends Mage_Catalog_Helper_Category {
	/**
	 * Этот метод отличается от родительского только наличием дополнительного кэширования.
	 * Благодаря кэшированию этот метод будет работать быстрее,
	 * чем родительский метод @see Mage_Catalog_Helper_Category::getStoreCategories().
	 * @override
	 * @param bool|string $sorted [optional]
	 * @param bool $asCollection [optional]
	 * @param bool $toLoad [optional]
	 * @return Mage_Catalog_Model_Resource_Category_Collection|Df_Catalog_Model_Category[]|Varien_Data_Tree_Node_Collection
	 */
	public function getStoreCategories($sorted = false, $asCollection = false, $toLoad = true) {
		/** @var Mage_Catalog_Model_Resource_Category_Collection|Df_Catalog_Model_Category[]|Varien_Data_Tree_Node_Collection $result */
		/** @var string $cacheKey */
		// Запрограммированное здесь кэширование
		// не работает в Magento Enterprise Edittion 1.13.0.1
		// (Serialization of 'Mage_Core_Model_Config_Element' is not allowed)
//		$cacheKey = Df_Catalog_Model_Cache::s()->makeKey(__METHOD__, array($sorted, $asCollection, $toLoad));
//		$result = Df_Catalog_Model_Cache::s()->loadDataComplex($cacheKey);
//		if (!$result) {
//			$result = parent::getStoreCategories($sorted, $asCollection, $toLoad);
//			Df_Catalog_Model_Cache::s()->saveDataComplex($cacheKey, $result);
//		}
//		return $result;
		return parent::getStoreCategories($sorted, $asCollection, $toLoad);
	}
}