<?php
class Df_Logging_Block_Adminhtml_System_Config_Actions
	extends Df_Adminhtml_Block_System_Config_Form_Field {
	/**
	 * @override
	 * @return string
	 */
	public function getTemplate() {return 'df/logging/system/config/actions.phtml';}

	/** @return array(string => string) */
	public function getLabels() {return Df_Logging_Model_Config::s()->getLabels();}

	/**
	 * @param string $key
	 * @return bool
	 */
	public function getIsChecked($key) {return Df_Logging_Model_Config::s()->isActive($key, true);}

	/**
	 * Render element html
	 *
	 * @param Varien_Data_Form_Element_Abstract $element
	 * @return string
	 */
	protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element) {
		$this->setNamePrefix($element->getName())->setHtmlId($element->getHtmlId());
		return $this->_toHtml();
	}
}