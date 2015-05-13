<?php
/**
 * Обратите внимание, что Magento не создаёт отдельные экземпляры данного класса
 * для вывода каждого поля!
 * Magento использует ЕДИНСТВЕННЫЙ экземпляр данного класса для вывода всех полей!
 * Поэтому в объектах данного класса нельзя кешировать информацию,
 * которая индивидуальна для поля конкретного поля!
 */
class Df_Catalog_Block_Admin_Conditions extends Df_Adminhtml_Block_System_Config_Form_Field {
	/**
	 * @override
	 * @param Varien_Data_Form_Element_Abstract $element
	 * @return string
	 */
	protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element) {
		return df_concat(
		   Df_Catalog_Model_Field_Conditions::i($element, $this)->getHtml()
			, rm_sprintf('<input type="hidden" value="0" name="%s"/>', $element->getData('name'))
		);
	}

	const _CLASS = __CLASS__;
}