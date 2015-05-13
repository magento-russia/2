<?php
class Df_Themes_Block_Infortis_Ultimo_Adminhtml_Button_Import_Cms
	extends Infortis_Ultimo_Block_Adminhtml_Button_Import_Cms {
	/**
	 * Цель перекрытия —
	 * перевод надписей на кнопках «import static blocks» и «import pages»
	 * в разделе импорта/экспорта самодельных блоков и страниц оформительской темы.
	 * @override
	 * @param Varien_Data_Form_Element_Abstract $element
	 * @return string
	 */
	protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element) {
		return strtr(parent::_getElementHtml($element), array(
			'<span>import static blocks</span>' => '<span>импорт</span>'
			,'<span>import pages</span>' => '<span>импорт</span>'
		));
	}
}