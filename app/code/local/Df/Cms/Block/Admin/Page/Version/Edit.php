<?php
class Df_Cms_Block_Admin_Page_Version_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {
	/**
	 * Перекрывать надо именно конструктор, а не метод @see _construct(),
	 * потому что родительский класс пихает инициализацию именно в конструктор.
	 * @see Mage_Adminhtml_Block_Widget_Form_Container::__construct()
	 * @override
	 * @return Df_Cms_Block_Admin_Page_Version_Edit
	 */
	public function __construct() {
		parent::__construct();
		$version = Mage::registry('cms_page_version');
		$config = Df_Cms_Model_Config::s();
		/* @var $config Df_Cms_Model_Config */
		// Add 'new button' depending on permission
		if ($config->canCurrentUserSaveVersion()) {
			$this
				->_addButton(
					'new'
					,array(
						'label' => df_h()->cms()->__('Save as New Version')
						,df_sprintf(
							'editForm.submit(%s);'
							,df_quote_single($this->getNewUrl())
						)
						,'class' => 'new'
					)
				)
			;
			$this->_addButton('new_revision', array(
				'label' => df_h()->cms()->__('New Revision...')
				,'onclick' => rm_admin_button_location($this->getNewRevisionUrl())
				,'class' => 'new'
			));
		}
		$isOwner = $config->isCurrentUserOwner($version->getUserId());
		$isPublisher = $config->canCurrentUserPublishRevision();
		// Only owner can remove version if he has such permissions
		if (!$isOwner || !$config->canCurrentUserDeleteVersion()) {
			$this->removeButton('delete');
		}
		// Only owner and publisher can save version
		if (($isOwner || $isPublisher) && $config->canCurrentUserSaveVersion()) {
			$this
				->_addButton(
					'saveandcontinue'
					,array(
						'label' => df_h()->cms()->__('Save and Continue Edit')
						,'onclick' => "editForm.submit($('edit_form').action+'back/edit/');"
						,'class' => 'save'
					)
					, 1
				)
			;
		}
		else {
			$this->removeButton('save');
		}
	}

	/**
	 * @override
	 * @return string
	 */
	public function getBackUrl() {
		return
			$this->getUrl(
				'*/cms_page/edit'
				,array(
					'page_id' => Mage::registry('cms_page')->getPageId()
					,'tab' => 'versions'
				)
			)
		;
	}

	/**
	 * @overrride
	 * @return string
	 */
	public function getDeleteUrl() {
		return $this->getUrl('*/*/delete', array('_current' => true));
	}

	/**
	 * @override
	 * @return string
	 */
	public function getHeaderText() {
		/** @var string $versionLabel */
		$versionLabel = df_e(Mage::registry('cms_page_version')->getLabel());
		if (!$versionLabel) {
			$versionLabel = df_h()->cms()->__('N/A');
		}
		return df_h()->cms()->__(
			"Edit Page '%s' Version '%s'", df_e(Mage::registry('cms_page')->getTitle()), $versionLabel
		);
	}

	/** @return string */
	public function getNewUrl() {return $this->getUrl('*/*/new', array('_current' => true));}

	/** @return string */
	public function getNewRevisionUrl() {
		return $this->getUrl('*/cms_page_revision/new', array('_current' => true));
	}
	/** @var string */
	protected $_blockGroup = 'df_cms';
	/** @var string */
	protected $_controller = 'adminhtml_cms_page_version';
	/** @var string */
	protected $_objectId   = 'version_id';
}