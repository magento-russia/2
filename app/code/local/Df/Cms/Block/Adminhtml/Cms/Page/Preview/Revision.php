<?php
class Df_Cms_Block_Adminhtml_Cms_Page_Preview_Revision extends Df_Core_Block_Admin {
	/**
	 * Retrieve id of currently selected revision
	 * @return int
	 */
	public function getRevisionId()
	{
		if (!$this->hasRevisionId()) {
			$this->setData('revision_id', (int)$this->getRequest()->getPost('preview_selected_revision'));
		}
		return $this->_getData('revision_id');
	}

	/**
	 * Prepare array with revisions sorted by versions
	 * @return array
	 */
	public function getRevisions()
	{
		/** @var Df_Cms_Model_Resource_Page_Revision_Collection $collection */
		$collection = Df_Cms_Model_Page_Revision::c();
		$collection
			->addPageFilter($this->getRequest()->getParam('page_id'))
			->joinVersions()
			->addNumberSort()
			->addVisibilityFilter(
				df_mage()->admin()->session()->getUser()->getId()
				,Df_Cms_Model_Config::s()->getAllowedAccessLevel()
			)
		;
		$revisions = array();
		foreach ($collection->getItems() as $item) {
			if (isset($revisions[$item->getVersionId()])) {
				$revisions[$item->getVersionId()]['revisions'][]= $item;
			} else {
				$revisions[$item->getVersionId()] = array(
					'revisions' => array($item),'label' => ($item->getLabel() ? $item->getLabel() : $this->__('N/A'))
				);
			}
		}
		krsort($revisions);
		reset($revisions);
		return $revisions;
	}
}