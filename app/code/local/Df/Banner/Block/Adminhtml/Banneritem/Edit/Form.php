<?php
class Df_Banner_Block_Adminhtml_Banneritem_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {
	/**
	 * @override
	 * @return Df_Banner_Block_Adminhtml_Banneritem_Edit_Form
	 * @throws Exception
	 */
	protected function _prepareForm() {
		/** @var Varien_Data_Form $form */
		$form = new Varien_Data_Form(array(
			'id' => 'edit_form'
			, 'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id')))
			, 'method' => 'post'
			, 'enctype' => 'multipart/form-data'
		));
		/** @noinspection PhpUndefinedMethodInspection */
		$form->setUseContainer(true);
		$this->setForm($form);
		parent::_prepareForm();
		return $this;
	}
}