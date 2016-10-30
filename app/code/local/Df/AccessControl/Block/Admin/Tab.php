<?php
class Df_AccessControl_Block_Admin_Tab
	extends Mage_Adminhtml_Block_Widget_Form
	implements Mage_Adminhtml_Block_Widget_Tab_Interface {
	/** @return bool */
	public function canShowTab() {return Df_AccessControl_Settings::s()->getEnabled();}

	/**
	 * @override
	 * @return string
	 */
	public function getTabLabel() {return 'Доступ к товарным разделам';}

	/**
	 * @override
	 * @return string
	 */
	public function getTabTitle() {return $this->getTabLabel();}

	/**
	 * @override
	 * @return string|null
	 */
	public function getTemplate() {return !$this->canShowTab() ? null : 'df/access_control/tab.phtml';}

	/**
	 * @override
	 * @return boolean
	 */
	public function isHidden() {return false;}

	/** @return bool */
	public function isModuleEnabled() {return $this->getRole()->isModuleEnabled();}

	/** @return string */
	public function renderCategoryTree() {return df_render('Df_AccessControl_Block_Admin_Tab_Tree');}

	/** @return string */
	public function renderStoreSwitcher() {
		return df_render('adminhtml/store_switcher', 'store/switcher/enhanced.phtml');
	}

	/** @return Df_AccessControl_Model_Role */
	private function getRole() {return Df_AccessControl_Model_Role::fromRequest();}
}