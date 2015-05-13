<?php
class Df_Cms_Block_Adminhtml_Cms_Page_Revision_Edit_Tab_Content
	extends Mage_Adminhtml_Block_Cms_Page_Edit_Tab_Content {
	/**
	 * Preparing form by adding extra fields.
	 * Adding on change js call.
	 * @return Df_Cms_Block_Adminhtml_Cms_Page_Revision_Edit_Tab_Content
	 */
	protected function _prepareForm()
	{
		/* @var $model Mage_Cms_Model_Page */
		$model = Mage::registry('cms_page');
		parent::_prepareForm();
		df_h()->cms()->addOnChangeToFormElements($this->getForm(), 'dataChanged();');
		/* @var $fieldset Varien_Data_Form_Element_Fieldset */
		$fieldset = $this->getForm()->getElement('content_fieldset');
		if ($model->getPageId()) {
			/**
			 * Обратите внимание,
			 * что нельзя применять цепной вызов $fieldset->addField()->addField(),
			 * потому что addField() возвращает не $fieldset, а созданное поле.
			 */
			$fieldset
				->addField(
					'page_id'
					,Df_Varien_Data_Form_Element_Abstract::TYPE__HIDDEN
					,array(
						'name' => 'page_id'
					)
				)
			;
			$fieldset
				->addField(
					'version_id'
					,Df_Varien_Data_Form_Element_Abstract::TYPE__HIDDEN
					,array(
						'name' => 'version_id'
					)
				)
			;
			$fieldset
				->addField(
					'revision_id'
					,Df_Varien_Data_Form_Element_Abstract::TYPE__HIDDEN
					,array(
						'name' => 'revision_id'
					)
				)
			;
			$fieldset
				->addField(
					'label'
					,Df_Varien_Data_Form_Element_Abstract::TYPE__HIDDEN
					,array(
						'name' => 'label'
					)
				)
			;
			$fieldset
				->addField(
					'user_id'
					,Df_Varien_Data_Form_Element_Abstract::TYPE__HIDDEN
					,array(
						'name' => 'user_id'
					)
				)
			;
		}
		$this->getForm()->setValues($model->getData());
		// setting current user id for new version functionality.
		// in posted data there will be current user
		$this->getForm()->getElement('user_id')->setValue(df_mage()->admin()->session()->getUser()->getId());
		return $this;
	}

	/**
	 * Check permission for passed action
	 * Rewrite CE save permission to EE save_revision
	 *
	 * @param string $action
	 * @return bool
	 */
	protected function _isAllowedAction($action)
	{
		if ('save' === $action) {
			$action = 'save_revision';
		}
		return parent::_isAllowedAction($action);
	}
}