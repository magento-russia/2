<?php
/**
 * @method Df_Admin_Model_Config_Form_FieldInstance_Info_Urls getInstance()
 */
class Df_Admin_Block_System_Config_Form_Field_Info_Urls
	extends Df_Admin_Block_System_Config_Form_Field_Custom {
	/**
	 * @override
	 * @return string
	 */
	public function getTemplate() {return 'df/admin/system/config/form/field/info/urls.phtml';}

	/**
	 * @override
	 * @return string
	 */
	protected function getInstanceClass() {
		return Df_Admin_Model_Config_Form_FieldInstance_Info_Urls::_CLASS;
	}

	/** @return array(string => string) */
	protected function getUrls() {return $this->getInstance()->getUrls();}
}