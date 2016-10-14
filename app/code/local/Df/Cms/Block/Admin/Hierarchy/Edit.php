<?php
class Df_Cms_Block_Admin_Hierarchy_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {
	/**
	 * Перекрывать надо именно конструктор, а не метод @see _construct(),
	 * потому что родительский класс пихает инициализацию именно в конструктор.
	 * @see Mage_Adminhtml_Block_Widget_Form_Container::__construct()
	 * @override
	 * @return Df_Cms_Block_Admin_Hierarchy_Edit
	 */
	public function __construct() {
		$this->_objectId   = 'node_id';
		$this->_blockGroup = 'df_cms';
		$this->_controller = 'adminhtml_cms_hierarchy';
		parent::__construct();
		$this->_updateButton('save', 'onclick', 'hierarchyNodes.save()');
		$this->_updateButton('save', 'label', df_h()->cms()->__('Save Pages Hierarchy'));
		$this->_removeButton('back');
		if (Df_Cms_Model_Hierarchy_Lock::s()->isLockedByOther()) {
			$confirmMessage = df_h()->cms()->__('Are you sure you want to break current lock?');
			$this->addButton('break_lock',array(
				'label'	=> df_h()->cms()->__('Unlock This Page')
				,'onclick' => rm_sprintf(
					'confirmSetLocation(%s, %s)'
					, df_quote_single($confirmMessage)
					, df_quote_single($this->getUrl('*/*/lock'))
				)
			));
			$this->_updateButton('save', 'disabled', true);
			$this->_updateButton('save', 'class', 'disabled');
		}
	}

	/**
	 * @override
	 * @return string
	 */
	public function getHeaderText() {
		return df_h()->cms()->__('Manage Pages Hierarchy');
	}
}