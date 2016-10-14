<?php
include('Df/Cms/controllers/Adminhtml/Cms/PageController.php');
class Df_Cms_Adminhtml_Cms_Page_RevisionController extends Df_Cms_Adminhtml_Cms_PageController {
	/**
	 * Delete action
	 */
	public function deleteAction()
	{
		// check if we know what should be deleted
		$id = $this->getRequest()->getParam('revision_id');
		if ($id) {
			$error = false;
			try {
				// init model and delete
				$revision = $this->_initRevision();
				$revision->delete();
				// display success message
				rm_session()->addSuccess(df_h()->cms()->__('Revision was successfully deleted.'));
				$this->_redirect('*/cms_page_version/edit', array(
						'page_id' => $revision->getPageId(),'version_id' => $revision->getVersionId()
					));
				return;
			} catch (Mage_Core_Exception $e) {
				// display error message
				rm_exception_to_session($e);
				$error = true;
			} catch (Exception $e) {
				Mage::logException($e);
				rm_session()->addError(df_h()->cms()->__('Error while deleting revision. Please try again later.'));
				$error = true;
			}

			// go back to edit form
			if ($error) {
				$this->_redirect('*/*/edit', array('_current' => true));
				return;
			}
		}
		// display error message
		rm_session()->addError(df_h()->cms()->__('Unable to find a revision to delete.'));
		// go to grid
		$this->_redirect('*/cms_page/edit', array('_current' => true));
	}

	/**
	 * Generates preview of page
	 * @return Df_Cms_Adminhtml_Cms_Page_RevisionController
	 */
	public function dropAction()
	{
		// check if data sent
		$data = $this->getRequest()->getPost();
		if (!empty($data) && isset($data['page_id'])) {
			// init model and set data
			$page = Mage::getSingleton('cms/page')
				->load($data['page_id']);
			if (!$page->getId()) {
				$this->_forward('noRoute');
				return $this;
			}

			/*
			 * If revision was selected load it and get data for preview from it
			 */
			$_tempData = null;
			if (isset($data['preview_selected_revision']) && $data['preview_selected_revision']) {
				$revision = $this->_initRevision($data['preview_selected_revision']);
				if ($revision->getId()) {
					$_tempData = $revision->getData();
				}
			}

			/*
			 * If there was no selected revision then use posted data
			 */
			if (is_null($_tempData)) {
				$_tempData = $data;
			}

			/*
			 * Posting posted data in page model
			 */
			$page->addData($_tempData);
			/*
			 * Retrieve store id from page model or if it was passed from post
			 */
			$selectedStoreId = $page->getStoreId();
			if (is_array($selectedStoreId)) {
				$selectedStoreId = array_shift($selectedStoreId);
			}
			if (isset($data['preview_selected_store']) && $data['preview_selected_store']) {
				$selectedStoreId = $data['preview_selected_store'];
			} else {
				if (!$selectedStoreId) {
					$selectedStoreId = Mage::app()->getDefaultStoreView()->getId();
				}
			}
			$selectedStoreId = (int) $selectedStoreId;
			/*
			 * Emulating front environment
			 */
			Mage::app()->getLocale()->emulate($selectedStoreId);
			Mage::app()->setCurrentStore(rm_store($selectedStoreId));
			Mage::getDesign()->setArea('frontend')
				->setStore($selectedStoreId);
			$designChange = Mage::getSingleton('core/design')
				->loadChange($selectedStoreId);
			if ($designChange->getData()) {
				Mage::getDesign()->setPackageName($designChange->getPackage())
					->setTheme($designChange->getTheme());
			}

			Mage::helper('cms/page')->renderPageExtended($this);
			Mage::app()->getLocale()->revert();
		} else {
			$this->_forward('noRoute');
		}
		return $this;
	}

	/**
	 * Edit revision of CMS page
	 * @override
	 * @return void
	 */
	public function editAction()
	{
		$revisionId = $this->getRequest()->getParam('revision_id');
		$revision = $this->_initRevision($revisionId);
		if ($revisionId && !$revision->getId()) {
			rm_session()->addError(
				df_h()->cms()->__('Could not load specified revision.'));
			$this->_redirect('*/cms_page/edit',array('page_id' => $this->getRequest()->getParam('page_id')));
			return;
		}
		$data = rm_session()->getFormData(true);
		if (!empty($data)) {
			$_data = $revision->getData();
			$_data = array_merge($_data, $data);
			$revision->setData($_data);
		}
		$this->_initAction()
			->_addBreadcrumb(df_h()->cms()->__('Edit Revision'),df_h()->cms()->__('Edit Revision'));
		$this->renderLayout();
	}

	/**
	 * New Revision action
	 * @return Df_Cms_Adminhtml_Cms_Page_RevisionController
	 */
	public function newAction()
	{
		$this->_forward('edit');
	}

	/**
	 * Controller predispatch method
	 * @return Mage_Adminhtml_Controller_Action
	 */
	public function preDispatch()
	{
		if ('drop' === $this->getRequest()->getActionName()) {
			$this->_currentArea = 'frontend';
		}
		parent::preDispatch();
		return $this;
	}

	/**
	 * Prepares page with iframe
	 * @return void
	 */
	public function previewAction() {
		// check if data sent
		$data = $this->getRequest()->getPost();
		if (empty($data) || !isset($data['page_id'])) {
			$this->_forward('noRoute');
			return;
		}

		$page = $this->_initPage();
		$this->loadLayout();
		$this
			->_title(null, true)
			->_title($this->__('Page Preview'))
			->_title($page->getTitle())
		;
		$stores = $page->getStoreId();
		if (isset($data['stores'])) {
			$stores = $data['stores'];
		}
		/*
		 * Checking if all stores passed then we should not assign array to block
		 */
		$allStores = false;
		if (
				is_array($stores)
			&&
				(1 === count($stores))
			&&
				!array_shift($stores)
		) {
			$allStores = true;
		}

		if (!$allStores) {
			$this->getLayout()->getBlock('store_switcher')->setStoreIds($stores);
		}

		// Setting default values for selected store and revision
		$data['preview_selected_store'] = 0;
		$data['preview_selected_revision'] = 0;
		$this->getLayout()->getBlock('preview_form')->setFormData($data);
		// Remove revision switcher if page is out of version control
		if (!$page->getUnderVersionControl()) {
			$this->getLayout()->getBlock('tools')->unsetChild('revision_switcher');
		}

		$this->renderLayout();
	}

	/**
	 * Publishing revision
	 */
	public function publishAction()
	{
		$revision = $this->_initRevision();
		try {
			$revision->publish();
			// display success message
			rm_session()->addSuccess(df_h()->cms()->__('Revision was successfully published.'));
			$this->_redirect('*/cms_page/edit', array('page_id' => $revision->getPageId()));
			return;
		} catch (Exception $e) {
			// display error message
			rm_exception_to_session($e);
			// redirect to edit form
			$this->_redirect('*/*/edit', array(
					'page_id' => $this->getRequest()->getParam('page_id'),'revision_id' => $this->getRequest()->getParam('revision_id')
					));
			return;
		}
	}

	/**
	 * Save action
	 * @return void
	 */
	public function saveAction()
	{
		// check if data sent
		$data = $this->getRequest()->getPost();
		if ($data) {
			$data = $this->_filterPostData($data);
			// init model and set data
			$revision = $this->_initRevision();
			$revision->setData($data)->setUserId(rm_admin_id());
			// try to save it
			try {
				// save the data
				$revision->save();
				// display success message
				rm_session()->addSuccess(df_h()->cms()->__('Revision was successfully saved.'));
				// clear previously saved data from session
				rm_session()->setFormData(false);
				// check if 'Save and Continue'
				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/' . $this->getRequest()->getParam('back'),array(
							'page_id' => $revision->getPageId(),'revision_id' => $revision->getId()
						));
					return;
				}
				// go to grid
				$this->_redirect('*/cms_page_version/edit', array(
						'page_id' => $revision->getPageId(),'version_id' => $revision->getVersionId()
					));
				return;
			} catch (Exception $e) {
				// display error message
				rm_exception_to_session($e);
				// save data in session
				rm_session()->setFormData($data);
				// redirect to edit form
				$this->_redirect('*/*/edit',array(
						'page_id' => $this->getRequest()->getParam('page_id'),'revision_id' => $this->getRequest()->getParam('revision_id'),));
				return;
			}
		}
		return;
	}

	/**
	 * Init actions
	 * @return Df_Cms_Adminhtml_Cms_Page_RevisionController
	 */
	protected function _initAction()
	{
		// load layout, set active menu and breadcrumbs
		$this->loadLayout()
			->_setActiveMenu('cms/page')
			->_addBreadcrumb(df_mage()->cmsHelper()->__('CMS'), df_mage()->cmsHelper()->__('CMS'))
			->_addBreadcrumb(df_mage()->cmsHelper()->__('Manage Pages'), df_mage()->cmsHelper()->__('Manage Pages'))
		;
		return $this;
	}

	/**
	 * Prepare and place revision model into registry
	 * with loaded data if id parameter present
	 *
	 * @param int $revisionId
	 * @return Df_Cms_Model_Page_Revision
	 */
	protected function _initRevision($revisionId = null)
	{
		if (is_null($revisionId)) {
			$revisionId = (int) $this->getRequest()->getParam('revision_id');
		}
		/** @var Df_Cms_Model_Page_Revision $revision */
		$revision = Df_Cms_Model_Page_Revision::i();
		$userId = rm_admin_id();
		$accessLevel = Df_Cms_Model_Config::s()->getAllowedAccessLevel();
		if ($revisionId) {
			$revision->loadWithRestrictions($accessLevel, $userId, $revisionId);
		} else {
			// loading empty revision
			$versionId = (int) $this->getRequest()->getParam('version_id');
			$pageId = (int) $this->getRequest()->getParam('page_id');
			// loading empty revision but with general data from page and version
			$revision->loadByVersionPageWithRestrictions($versionId, $pageId, $accessLevel, $userId);
			$revision->setUserId($userId);
		}

		//setting in registry as cms_page to make work CE blocks
		Mage::register('cms_page', $revision);
		return $revision;
	}

	/**
	 * Check the permission to run it
	 * @return boolean
	 */
	protected function _isAllowed() {
		/** @var bool $result */
		$result = false;
		switch($this->getRequest()->getActionName()) {
			case 'save':
				$result = Df_Cms_Model_Config::s()->canCurrentUserSaveRevision();
				break;
			case 'publish':
				$result = Df_Cms_Model_Config::s()->canCurrentUserPublishRevision();
				break;
			case 'delete':
				$result = Df_Cms_Model_Config::s()->canCurrentUserDeleteRevision();
				break;
			default:
				$result = rm_admin_allowed('cms/page');
				break;
		}
		return $result;
	}
}