<?php
class Df_Cms_Block_Adminhtml_Cms_Page_Revision_Edit extends Mage_Adminhtml_Block_Cms_Page_Edit {
	/**
	 * @override
	 * @return string
	 */
	public function getBackUrl() {
		return
			$this->getUrl(
				'*/cms_page_version/edit'
				,array(
					'page_id' => Mage::registry('cms_page')->getPageId()
					,'version_id' => Mage::registry('cms_page')->getVersionId()
			 	)
			)
		;
	}

	/**
	 * @override
	 * @return string
	 */
	public function getDeleteUrl() {
		return $this->getUrl('*/*/delete', array('_current' => true));
	}

	/**
	 * @override
	 * @return string
	 */
	public function getFormHtml() {
		return $this->getChildHtml('revision_info') . parent::getFormHtml();
	}

	/**
	 * @override
	 * @return string
	 */
	public function getHeaderText() {
		$revisionNumber = Mage::registry('cms_page')->getRevisionNumber();
		$title = df_text()->escapeHtml(Mage::registry('cms_page')->getTitle());
		if ($revisionNumber) {
			return
				df_h()->cms()->__(
					"Edit Page '%s' Revision #%s"
					,
					$title
					,
					df_text()->escapeHtml($revisionNumber)
				)
			;
		} else {
			return df_h()->cms()->__("Edit Page '%s' New Revision", $title);
		}
	}

	/** @return string */
	public function getNewVersionUrl() {
		return $this->getUrl('*/cms_page_version/new');
	}

	/**
	 * @override
	 * @return string
	 */
	public function getPreviewUrl() {
		return $this->getUrl('*/*/preview');
	}

	/** @return string */
	public function getPublishUrl() {
		return $this->getUrl('*/*/publish', array('_current' => true));
	}

	/**
	 * @override
	 * @param string $action
	 * @return bool
	 */
	protected function _isAllowedAction($action) {
		return  parent::_isAllowedAction(('save' === $action) ? 'save_revision' : $action);
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->removeButton('delete');
		$this->_objectId = 'revision_id';
		$this->_controller = 'adminhtml_cms_page_revision';
		$this->_blockGroup = 'df_cms';
		/* @var $config Df_Cms_Model_Config */
		$config = Df_Cms_Model_Config::s();
		$this->setFormActionUrl($this->getUrl('*/cms_page_revision/save'));
		$objId = $this->getRequest()->getParam($this->_objectId);
		if (!empty($objId) && $config->canCurrentUserDeleteRevision()) {
			$this
				->_addButton(
					'delete_revision'
					,array(
						'label' => df_h()->cms()->__('Delete')
						,'class' => 'delete'
						,'onclick' =>
							rm_sprintf(
								'deleteConfirm(%s, %s)'
								,df_quote_single(df_h()->cms()->__('Are you sure you want to delete this revision?'))
								,df_quote_single($this->getDeleteUrl())
							)
					)
				)
			;
		}
		$this
			->_addButton(
				'preview'
				,array(
					'label' => df_h()->cms()->__('Preview')
					,'onclick' =>
						rm_sprintf(
							'previewAction(%s, editForm, %s)'
							,df_quote_single('edit_form')
							,df_quote_single($this->getPreviewUrl())
						)
					,'class' => 'preview'
				)
			)
		;
		if ($config->canCurrentUserPublishRevision()) {
			$this
				->_addButton(
					'publish'
					,array(
						'id' => 'publish_button'
						,'label' => df_h()->cms()->__('Publish')
						,'onclick' =>
							rm_sprintf(
								'publishAction(%s)'
								,df_quote_single($this->getPublishUrl())
							)
						,'class' => 'publish' . (Mage::registry('cms_page')->getId()? '' : ' no-display')
					)
					, 1
				)
			;
			if ($config->canCurrentUserSaveRevision()) {
				$this
					->_addButton(
						'save_publish'
						,array(
							'id' => 'save_publish_button'
							,'label' => df_h()->cms()->__('Save and Publish')
							,'onclick' =>
								rm_sprintf(
									'saveAndPublishAction(editForm, %s)'
									,df_quote_single($this->getSaveUrl())
								)
							,'class' => 'publish no-display'
						)
						, 1
					)
				;
			}
			$this->_updateButton('saveandcontinue', 'level', 2);
		}
		if ($config->canCurrentUserSaveRevision()) {
			$this->_updateButton('save', 'label', df_h()->cms()->__('Save'));
			$this
				->_updateButton(
					'save'
					,'onclick'
					,rm_sprintf(
						'editForm.submit(%s);'
						,df_quote_single($this->getSaveUrl())
					)
				)
			;
			$this
				->_updateButton(
					'saveandcontinue'
					,'onclick'
					,rm_sprintf(
						'editForm.submit(%s);'
						,df_quote_single($this->getSaveUrl() . 'back/edit/')
					)
				)
			;
			// Adding button to create new version
			$this
				->_addButton(
					'new_version'
					,array(
						'id' => 'new_version'
						,'label' => df_h()->cms()->__('Save in New Version...')
						,'onclick' => 'newVersionAction()'
						,'class' => 'new'
					)
				)
			;
			$this->_formScripts[]= "
				function newVersionAction(){
					var versionName = prompt('" . df_h()->cms()->__('Specify New Version Name (required)') . "', '')
					if ('' === versionName) {
						alert('" . df_h()->cms()->__('You should specify valid name') . "');
						return false;
					} else if (null === versionName) {
						return false;
					}

					$('page_label').value = versionName;
					editForm.submit('" . $this->getNewVersionUrl() . "');
				}
			";
		} else {
			$this->removeButton('save');
			$this->removeButton('saveandcontinue');
		}
	}
}