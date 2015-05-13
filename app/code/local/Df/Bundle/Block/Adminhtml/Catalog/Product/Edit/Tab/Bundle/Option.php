<?php
class Df_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Bundle_Option
	extends Mage_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Bundle_Option {
	/**
	 * Цель перекрытия —
	 * перевести надпись на кнопке «Delete Option»
	 * на административном экране товарного комплекта.
	 * @override
	 * @return Df_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Bundle_Option
	 */
	protected function _prepareLayout() {
		parent::_prepareLayout();
		/** @var Mage_Adminhtml_Block_Widget_Button $buttonDelete */
		$buttonDelete = $this->getChild('option_delete_button');
		if (false === $buttonDelete) {
			$buttonDelete = null;
		}
		if (!is_null($buttonDelete)) {
			$buttonDelete->setData('label', df_mage()->bundleHelper()->__('Delete Option'));
		}
		return $this;
	}
}