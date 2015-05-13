<?php
class Df_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Attributes extends Mage_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Attributes {
	/**
	 * Функция Mage_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Attributes::_prepareForm()
	 * является причиной ошибки JavaScript:
	 * объект $('price_type') используется до загрузки на страницу элемента 'price_type'.
	 *
	 * Данная заплатка устраняет ошибку: мы откладываем операции с $('price_type') до загрузки DOM.
	 * @override
	 * @return void
	 */
	protected function _prepareForm() {
		parent::_prepareForm();
		if (
				df_enabled(Df_Core_Feature::TWEAKS_ADMIN)
			&&
				df_cfg()->admin()->catalog()->product()->getFixBundleJs()
		) {
			if ($tax = $this->getForm()->getElement('tax_class_id')) {
				$tax->setAfterElementHtml(
					'<script type="text/javascript">'
					. "
					function changeTaxClassId() {
						if ($('price_type').value === '" . Mage_Bundle_Model_Product_Price::PRICE_TYPE_DYNAMIC . "') {
							$('tax_class_id').disabled = true;
							$('tax_class_id').value = '0';
							$('tax_class_id').removeClassName('required-entry');
							if ($('advice-required-entry-tax_class_id')) {
								$('advice-required-entry-tax_class_id').remove();
							}
						} else {
							$('tax_class_id').disabled = false;
							" . ($tax->getRequired() ? "$('tax_class_id').addClassName('required-entry');" : '') . "
						}
					}

					// BEGIN PATCH
					Event.observe(window, 'load', function() {
						$('price_type').observe('change', changeTaxClassId);
						changeTaxClassId();
					});
					// END PATCH

					"
					. '</script>'
				);
			}
		}
	}
}