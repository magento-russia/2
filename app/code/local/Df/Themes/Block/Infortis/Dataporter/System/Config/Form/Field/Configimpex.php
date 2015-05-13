<?php
class Df_Themes_Block_Infortis_Dataporter_System_Config_Form_Field_Configimpex
	extends Infortis_Dataporter_Block_System_Config_Form_Field_Configimpex {
	/**
	 * Цель перекрытия —
	 * перевод надписей на кнопках «Import» и «Export»
	 * и фразы «Click to go to the import/export page»
	 * в разделе импорта/экспорта настроек оформительской темы.
	 * @override
	 * @param Varien_Data_Form_Element_Abstract $element
	 * @return string
	 */
	public function render(Varien_Data_Form_Element_Abstract $element) {
		/** @var array(string => mixed) $originalData */
		$originalData = $element->getData('original_data');
		/** @var Infortis_Dataporter_Helper_Data $helper */
		$helper = Mage::helper('dataporter');
		$originalData['sublabel'] = $helper->__(df_a($originalData, 'sublabel'));
		$element->setData('original_data', $originalData);
		return strtr(parent::render($element), array(
			'<span>export</span>' => '<span>экспорт</span>'
			,'<span>import</span>' => '<span>импорт</span>'
		));
	}
}