<?php
class Df_Catalog_Model_Config_Source_Category_Content_Position {
	/** @return array(array(string => string|int)) */
	public function toOptionArray() {
		return rm_map_to_options(array(
			self::DF_BEFORE_STATIC_BLOCK => 'Над самодельным блоком'
			,self::DF_BEFORE_PRODUCTS => 'Под самодельным блоком, но над товарами'
			,self::DF_AFTER_PRODUCTS => 'Под товарами'
			,self::DF_BEFORE_AND_AFTER_PRODUCTS => 'Над товарами и под товарами'
		));
	}
	const DF_BEFORE_STATIC_BLOCK = 'before_static_block';
	const DF_BEFORE_PRODUCTS = 'before_products';
	const DF_AFTER_PRODUCTS = 'after_products';
	const DF_BEFORE_AND_AFTER_PRODUCTS = 'before_and_after_products';
}