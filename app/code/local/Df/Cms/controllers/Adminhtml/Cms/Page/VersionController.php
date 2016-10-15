<?php
include('Df/Cms/controllers/Adminhtml/Cms/PageController.php');
class Df_Cms_Adminhtml_Cms_Page_VersionController extends Df_Cms_Adminhtml_Cms_PageController {
	/**
	 * Delete action
	 * @return void
	 */
	public function deleteAction()
	{
		// check if we know what should be deleted
		$id = $this->getRequest()->getParam('version_id');
		if ($id) {
			 // init model
			$version = $this->_initVersion();
			$error = false;
			try {
				$version->delete();
				// display success message
				df_session()->addSuccess(df_h()->cms()->__('Version was successfully deleted.'));
				$this->_redirect('*/cms_page/edit', array('page_id' => $version->getPageId()));
				return;
			} catch (Mage_Core_Exception $e) {
				// display error message
				df_exception_to_session($e);
				$error = true;
			} catch (Exception $e) {
				Mage::logException($e);
				df_session()->addError(df_h()->cms()->__('Error while deleting version. Please try again later.'));
				$error = true;
			}

			// go back to edit form
			if ($error) {
				$this->_redirect('*/*/edit', array('_current' => true));
				return;
			}
		}
		// display error message
		df_session()->addError(df_h()->cms()->__('Unable to find a version to delete.'));
		// go to grid
		$this->_redirect('*/cms_page/edit', array('_current' => true));
	}

	/**
	 * Edit version of CMS page
	 * @return void
	 */
	public function editAction()
	{
		$version = $this->_initVersion();
		if (!$version->getId()) {
			df_session()->addError(
				df_h()->cms()->__('Could not load specified version.'));
			$this->_redirect('*/cms_page/edit',array('page_id' => $this->getRequest()->getParam('page_id')));
			return;
		}
		$page = $this->_initPage();
		$data = df_session()->getFormData(true);
		if (!empty($data)) {
			$_data = $version->getData();
			$_data = array_merge($_data, $data);
			$version->setData($_data);
		}
		$this->_initAction()
			->_addBreadcrumb(df_h()->cms()->__('Edit Version'),df_h()->cms()->__('Edit Version'));
		$this->renderLayout();
	}

	/**
	 * Mass deletion for revisions
	 * @return Df_Cms_Adminhtml_Cms_Page_VersionController
	 */
	public function massDeleteRevisionsAction()
	{
		$ids = $this->getRequest()->getParam('revision');
		if (!is_array($ids)) {
			df_session()->addError($this->__('Please select revision(s)'));
		}
		else {
			try {
				$accessLevel = Df_Cms_Model_Config::s()->getAllowedAccessLevel();
				foreach ($ids as $id) {
					$revision = Df_Cms_Model_Page_Revision::s()->loadWithRestrictions(
						$accessLevel, rm_admin_id(), $id
					);
					if ($revision->getId()) {
						$revision->delete();
					}
				}
				df_session()->addSuccess(
					$this->__('Total of %d record(s) were successfully deleted', count($ids))
				);
			} catch (Mage_Core_Exception $e) {
				df_exception_to_session($e);
			} catch (Exception $e) {
				Mage::logException($e);
				df_session()->addError(df_h()->cms()->__('Error while deleting revisions. Please try again later.'));
			}
		}
		$this->_redirect('*/*/edit', array('_current' => true, 'tab' => 'revisions'));
		return $this;
	}

	/**
	 * New Version
	 * @return void
	 */
	public function newAction()
	{
		// check if data sent
		$data = $this->getRequest()->getPost();
		if ($data) {
			// init model and set data
			$version = $this->_initVersion();
			$version->addData($data)->unsetData($version->getIdFieldName());
			// only if user not specified we set current user as owner
			if (!$version->getUserId()) {
				$version->setUserId(rm_admin_id());
			}
			if (isset($data['revision_id'])) {
				$data = $this->_filterPostData($data);
				$version->setInitialRevisionData($data);
			}
			// try to save it
			try {
				$version->save();
				// display success message
				df_session()->addSuccess(df_h()->cms()->__('New version was successfully created.'));
				// clear previously saved data from session
				df_session()->setFormData(false);
				if (isset($data['revision_id'])) {
					$this->_redirect('*/cms_page_revision/edit', array(
						'page_id' => $version->getPageId(),'revision_id' => $version->getLastRevision()->getId()
					));
				} else {
					$this->_redirect('*/cms_page_version/edit', array(
						'page_id' => $version->getPageId(),'version_id' => $version->getId()
					));
				}
				return;
			} catch (Exception $e) {
				// display error message
				df_exception_to_session($e);
				if ($this->_getRefererUrl()) {
					// save data in session
					df_session()->setFormData($data);
				}
				// redirect to edit form
				$this->_redirectReferer($this->getUrl('*/cms_page/edit',array('page_id' => $this->getRequest()->getParam('page_id'))));
				return;
			}
		}
	}

	/**
	 * Action for ajax grid with revisions
	 * @return Df_Cms_Adminhtml_Cms_Page_VersionController
	 */
	public function revisionsAction()
	{
		$this->_initVersion();
		$this->_initPage();
		$this->loadLayout();
		$this->renderLayout();
		return $this;
	}

	/**
	 * Save Action
	 * @return void
	 */
	public function saveAction()
	{
		// check if data sent
		$data = $this->getRequest()->getPost();
		if ($data) {
			// init model and set data
			$version = $this->_initVersion();
			// if current user not publisher he can't change owner
			if (!Df_Cms_Model_Config::s()->canCurrentUserPublishRevision()) {
				unset($data['user_id']);
			}
			$version->addData($data);
			// try to save it
			try {
				// save the data
				$version->save();
				// display success message
				df_session()->addSuccess(df_h()->cms()->__('Version was successfully saved.'));
				// clear previously saved data from session
				df_session()->setFormData(false);
				// check if 'Save and Continue'
				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/' . $this->getRequest()->getParam('back'),array(
							'page_id' => $version->getPageId(),'version_id' => $version->getId()
						));
					return;
				}
				// go to grid
				$this->_redirect('*/cms_page/edit', array('page_id' => $version->getPageId()));
				return;
			} catch (Exception $e) {
				// display error message
				df_exception_to_session($e);
				// save data in session
				df_session()->setFormData($data);
				// redirect to edit form
				$this->_redirect('*/*/edit',array(
						'page_id' => $this->getRequest()->getParam('page_id'),'version_id' => $this->getRequest()->getParam('version_id'),));
				return;
			}
		}
	}

	/**
	 * Init actions
	 * @return Df_Cms_Adminhtml_Cms_Page_VersionController
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
	 * Prepare and place version's model into registry
	 * with loaded data if id parameter present
	 *
	 * @param int $versionId
	 * @return Df_Cms_Model_Page_Version
	 */
	protected function _initVersion($versionId = null)
	{
		if (is_null($versionId)) {
			$versionId = (int) $this->getRequest()->getParam('version_id');
		}
		/* @var Df_Cms_Model_Page_Version $version */
		$version = Df_Cms_Model_Page_Version::i();
		if ($versionId) {
			$accessLevel = Df_Cms_Model_Config::s()->getAllowedAccessLevel();
			$version->loadWithRestrictions($accessLevel, rm_admin_id(), $versionId);
		}
		Mage::register('cms_page_version', $version);
		return $version;
	}

	/**
	 * Check the permission to run it
	 * May be in future there will be separate permissions for operations with version
	 * @return boolean
	 */
	protected function _isAllowed() {
		/** @var bool $result */
		$result = false;
		switch($this->getRequest()->getActionName()) {
			case 'new':
			case 'save':
				$result = Df_Cms_Model_Config::s()->canCurrentUserSaveVersion();
				break;
			case 'delete':
				$result = Df_Cms_Model_Config::s()->canCurrentUserDeleteVersion();
				break;
			case 'massDeleteRevisions':
				$result = Df_Cms_Model_Config::s()->canCurrentUserDeleteRevision();
				break;
			default:
				$result = rm_admin_allowed('cms/page');
				break;
		}
		return $result;
	}
}