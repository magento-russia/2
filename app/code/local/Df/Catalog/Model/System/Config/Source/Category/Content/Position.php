<?php
class Df_Catalog_Model_System_Config_Source_Category_Content_Position {
	const DF_BEFORE_STATIC_BLOCK = 'before_static_block';
	const DF_BEFORE_PRODUCTS = 'before_products';
	const DF_AFTER_PRODUCTS = 'after_products';
	const DF_BEFORE_AND_AFTER_PRODUCTS = 'before_and_after_products';
	public function toOptionArray() {
		return
			array(
				array(
					'value' => self::DF_BEFORE_STATIC_BLOCK
					,'label' => df_h()->catalog()->__('Над самодельным блоком')
				)
				,array(
					'value' => self::DF_BEFORE_PRODUCTS
					,'label' => df_h()->catalog()->__('Под самодельным блоком, но над товарами')
				)
				,array(
					'value' => self::DF_AFTER_PRODUCTS
					,'label' => df_h()->catalog()->__('Под товарами')
				)
				,array(
					'value' => self::DF_BEFORE_AND_AFTER_PRODUCTS
					,'label' => df_h()->catalog()->__('Над товарами и под товарами')
				)
			)
		;
	}

}