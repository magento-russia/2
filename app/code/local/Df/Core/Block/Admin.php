<?php
/**
 * Этот класс не является наследником класса Mage_Adminhtml_Block_Template,
 * но реализует все его методы и, таким образом,
 * может использоваться в качестве заменителя класса Mage_Adminhtml_Block_Template
 * для заимствованных модулей, в то же время добавляя к ним новую функциональность.
 */
class Df_Core_Block_Admin extends Df_Core_Block_Template_NoCache {
	/** @return mixed */
	public function getFormKey() {return rm_session_core()->getFormKey();}

	/**
	* Check whether or not the module output is enabled
	* Because many module blocks belong to Adminhtml module,
	* the feature "Disable module output" doesn't cover Admin area
	* @param string $moduleName Full module name
	* @return boolean
	*/
	public function isOutputEnabled($moduleName = null) {
		if ($moduleName === null) {
			$moduleName = $this->getModuleName();
		}
		return !Mage::getStoreConfigFlag('advanced/modules_disable_output/' . $moduleName);
	}

	/** @return string */
	protected function _getUrlModelClass(){return 'adminhtml/url';}

	/**
	 * @override
	 * @return string
	 */
	protected function _toHtml(){
		Mage::dispatchEvent('adminhtml_block_html_before', array('block' => $this));
		return parent::_toHtml();
	}

	const _CLASS = __CLASS__;
}