<?php
class Df_CustomerBalance_Block_Adminhtml_Customer_Edit_Tab_Customerbalance
	extends Mage_Adminhtml_Block_Widget
	implements Mage_Adminhtml_Block_Widget_Tab_Interface {
	/**
	 * @override
	 * @return string
	 */
	public function getAfter() {return 'tags';}
	/**
	 * @override
	 * @return bool
	 */
	public function canShowTab() {return true;}
	/**
	 * @override
	 * @return bool
	 */
	public function getSkipGenerateContent() {return true;}
	/**
	 * @override
	 * @return string
	 */
	public function getTabClass() {return 'ajax';}
	/**
	 * @override
	 * @return string
	 */
	public function getTabLabel() {return $this->getTitle();}
	/**
	 * @override
	 * @return string
	 */
	public function getTabTitle() {return $this->getTitle();}
	/**
	 * @override
	 * @return string
	 */
	public function getTabUrl() {
		return $this->getUrl('*/customerbalance/form', array('_current' => true));
	}
	/**
	 * @override
	 * @return bool
	 */
	public function isHidden() {return !$this->getRequest()->getParam('id');}
	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->setId('customerbalance');
		$this->setTitle(Df_CustomerBalance_Helper_Data::s()->__('Store Credit'));
	}
}