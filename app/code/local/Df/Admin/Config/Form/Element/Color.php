<?php
class Df_Admin_Config_Form_Element_Color extends Varien_Data_Form_Element_Text {
	/**
	 * @override
	 * @return string
	 */
	public function getHtml() {
		$this->addClass('rm-color-picker');
		return parent::getHtml();
	}
}