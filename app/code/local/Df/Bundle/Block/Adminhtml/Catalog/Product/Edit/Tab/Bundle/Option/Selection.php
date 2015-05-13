<?php
class Df_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Bundle_Option_Selection
	extends Mage_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Bundle_Option_Selection {
	/**
	 * Цель перекрытия —
	 * отсылка оповещения JavaScript bundle.product.edit.bundle.option.selection
	 * для перевода посредством JavaScript некоторых надписей
	 * на административном экране товарного комплекта.
	 * @override
	 * @return string
	 */
	protected function _toHtml() {
		return df_concat(
			parent::_toHtml()
			,"<script type='text/javascript'>
				jQuery(window).trigger('bundle.product.edit.bundle.option.selection');
			</script>"
		);
	}
}