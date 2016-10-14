<?php
class Df_Cms_Block_Admin_Page_Version_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {
	/**
	 * Define customized form template
	 */
	protected function _construct()
	{
		parent::_construct();
		$this->setTemplate('df/cms/page/version/form.phtml');
	}

	/**
	 * Preparing from for version page
	 * @return Df_Cms_Block_Admin_Page_Revision_Edit_Form
	 */
	protected function _prepareForm()
	{
		$form = new Varien_Data_Form(array(
				'id' => 'edit_form','action' => $this->getUrl('*/*/save', array('_current' => true)),'method' => 'post'
			));
		$form->setUseContainer(true);
		/* @var $model Mage_Cms_Model_Page */
		$version = Mage::registry('cms_page_version');
		$config = Df_Cms_Model_Config::s();
		/* @var $config Df_Cms_Model_Config */

		$isOwner = $config->isCurrentUserOwner($version->getUserId());
		$isPublisher = $config->canCurrentUserPublishRevision();
		$fieldset =
			$form->addFieldset(
				'version_fieldset'
				,array(
					'legend' => df_h()->cms()->__('Version Information')
					,'class' => 'fieldset-wide'
				)
			)
		;
		/**
		 * Обратите внимание,
		 * что нельзя применять цепной вызов $fieldset->addField()->addField(),
		 * потому что @uses Varien_Data_Form_Element_Fieldset::addField()
		 * возвращает не $fieldset, а созданное поле.
		 */
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
				'page_id'
				,Df_Varien_Data_Form_Element_Abstract::TYPE__HIDDEN
				,array(
					'name' => 'page_id'
				)
			)
		;
		$fieldset
			->addField(
				'label'
				,'text'
				,array(
					'name' => 'label'
					,'label' => df_h()->cms()->__('Version Label')
					,'disabled' => !$isOwner
					,'required' => true
				)
			)
		;
		$fieldset
			->addField(
				'access_level'
				,'select'
				,array(
					'label' => df_h()->cms()->__('Access Level')
					,'title' => df_h()->cms()->__('Access Level')
					,'name' => 'access_level'
					,'options' => df_h()->cms()->getVersionAccessLevels()
					,'disabled' => !$isOwner && !$isPublisher
				)
			)
		;
		if ($isPublisher) {
			$fieldset
				->addField(
					'user_id'
					,'select'
					,array(
						'label' => df_h()->cms()->__('Owner')
						,'title' => df_h()->cms()->__('Owner')
						,'name' => 'user_id'
						,'options' => df_h()->cms()->getUsersArray(!$version->getUserId())
						,'required' => !$version->getUserId()
					)
				)
			;
		}
		$form->setValues($version->getData());
		$this->setForm($form);
		return parent::_prepareForm();
	}
}