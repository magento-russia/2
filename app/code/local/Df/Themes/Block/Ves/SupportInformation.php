<?php
class Df_Themes_Block_Ves_SupportInformation
	extends Df_Core_Block_Template
	implements Varien_Data_Form_Element_Renderer_Interface {
	/**
	 * 2015-08-22
	 * Цель перекрытия —
	 * показ вместо мусора действительно полезной русскоязычным клиентам информации.
	 * Оформительская тема Ves Super Store (ThemeForest 8002349)
	 * http://themeforest.net/item/ves-super-store-responsive-magento-theme-/8002349?ref=dfediuk
	 * http://demoleotheme.com/superstore/
	 * http://magento-forum.ru/forum/370/
	 * @override
	 * @see Varien_Data_Form_Element_Renderer_Interface::()
	 * @param Varien_Data_Form_Element_Abstract $element
	 * @return string
	 */
	public function render(Varien_Data_Form_Element_Abstract $element) {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->toHtml();
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2015-08-22
	 * @override
	 * @see Df_Core_Block_Template::getDefaultTemplate()
	 * @return string
	 */
	protected function getDefaultTemplate() {return 'df/themes/ves/support-information.phtml';}
}