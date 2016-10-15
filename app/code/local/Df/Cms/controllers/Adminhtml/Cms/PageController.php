<?php
include('Mage/Adminhtml/controllers/Cms/PageController.php');
class Df_Cms_Adminhtml_Cms_PageController extends Mage_Adminhtml_Cms_PageController {
	/**
	 * Edit CMS page
	 */
	public function editAction()
	{
		$page = $this->_initPage();
		$data = df_session()->getFormData(true);
		if (! empty($data)) {
			$page->setData($data);
		}

		if ($page->getId()){
			if ($page->getUnderVersionControl()) {
				$this->_handles[]= 'adminhtml_cms_page_edit_changes';
			}
		} else if (!$page->hasUnderVersionControl()) {
			$page->setUnderVersionControl((int)Df_Cms_Model_Config::s()->getDefaultVersioningStatus());
		}
		$this->_title($page->getId() ? $page->getTitle() : $this->__('New Page'));
		$this->_initAction()
			->_addBreadcrumb($page->getId() ? df_mage()->cmsHelper()->__('Edit Page')
					: df_mage()->cmsHelper()->__('New Page'),$page->getId() ? df_mage()->cmsHelper()->__('Edit Page')
					: df_mage()->cmsHelper()->__('New Page'));
		$this->renderLayout();
	}

	/**
	 * Mass deletion for versions
	 *
	 */
	public function massDeleteVersionsAction()
	{
		if (!df_cfg()->cms()->versioning()->isEnabled()) {
			$this->_forward('denied');
			$this->setFlag('', self::FLAG_NO_DISPATCH, true);
		}
		else {
			$ids = $this->getRequest()->getParam('version');
			if (!is_array($ids)) {
				df_session()->addError($this->__('Please select version(s)'));
			}
			else {
				try {
					$accessLevel = Df_Cms_Model_Config::s()->getAllowedAccessLevel();
					foreach ($ids as $id) {
						$version = Df_Cms_Model_Page_Version::s()->loadWithRestrictions(
							$accessLevel, df_admin_id(), $id
						);
						if ($version->getId()) {
							$version->delete();
						}
					}
					df_session()->addSuccess($this->__(
						'Total of %d record(s) were successfully deleted', count($ids)
					));
				}
				catch (Mage_Core_Exception $e) {
					df_exception_to_session($e);
				}
				catch (Exception $e) {
					Mage::logException($e);
					df_session()->addError(df_h()->cms()->__(
						'Error while deleting versions. Please try again later.'
					));
				}
			}
			$this->_redirect('*/*/edit', array('_current' => true, 'tab' => 'versions'));
		}
		return $this;
	}

	/**
	 * Action for versions ajax tab
	 * @return Df_Cms_Adminhtml_Cms_Page_RevisionController
	 */
	public function versionsAction()
	{
		if (!df_cfg()->cms()->versioning()->isEnabled()) {
			$this->_forward('denied');
			$this->setFlag('', self::FLAG_NO_DISPATCH, true);
		}
		else {
			$this->_initPage();
			$this->loadLayout();
			$this->renderLayout();
		}
		return $this;
	}

	/**
	 * Init actions
	 * @override
	 * @return Df_Cms_Adminhtml_Cms_PageController
	 */
	protected function _initAction()
	{
		if (
			!(
					df_cfg()->cms()->versioning()->isEnabled()
				||
					df_cfg()->cms()->hierarchy()->isEnabled()
			)
		) {
			parent::_initAction();
		}
		else {
			$update = $this->getLayout()->getUpdate();
			$update->addHandle('default');
			// add default layout handles for this action
			$this->addActionLayoutHandles();
			$update->addHandle($this->_handles);
			$this->loadLayoutUpdates()
				->generateLayoutXml()
				->generateLayoutBlocks();
			$this->_initLayoutMessages('adminhtml/session');
			//load layout, set active menu and breadcrumbs
			$this->_setActiveMenu('cms/page')
				->_addBreadcrumb(df_mage()->cmsHelper()->__('CMS'), df_mage()->cmsHelper()->__('CMS'))
				->_addBreadcrumb(df_mage()->cmsHelper()->__('Manage Pages'), df_mage()->cmsHelper()->__('Manage Pages'));
			$this->_isLayoutLoaded = true;
		}
		return $this;
	}

	/**
	 * Prepare and place cms page model into registry
	 * with loaded data if id parameter present
	 *
	 * @param string $idFieldName
	 * @return Mage_Cms_Model_Page
	 */
	protected function _initPage()
	{
		$this->_title($this->__('CMS'))->_title($this->__('Pages'));
		$pageId = (int) $this->getRequest()->getParam('page_id');
		/** @var Df_Cms_Model_Page $page */
		$page = Df_Cms_Model_Page::i();
		if ($pageId) {
			$page->load($pageId);
		}
		Mage::register('cms_page', $page);
		return $page;
	}

	/**
	 * Check the permission to run action.
	 * @override
	 * @return boolean
	 */
	protected function _isAllowed()
	{
		switch($this->getRequest()->getActionName()) {
			case 'massDeleteVersions':
				return Df_Cms_Model_Config::s()->canCurrentUserDeleteVersion();
				break;
			default:
				return parent::_isAllowed();
				break;
		}
	}

	protected $_handles = array();
}