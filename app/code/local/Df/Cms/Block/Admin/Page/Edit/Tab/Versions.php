<?php
class Df_Cms_Block_Admin_Page_Edit_Tab_Versions
	extends Mage_Adminhtml_Block_Widget_Grid
	implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
	/**
	 * Array of admin users in system
	 * @var array
	 */
	protected $_usersHash = null;

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->setUseAjax(true);
		$this->setId('versions');
		$this->setSaveParametersInSession(true);
	}

	/**
	 * Prepares collection of versions
	 * @return Df_Cms_Block_Admin_Page_Edit_Tab_Versions
	 */
	protected function _prepareCollection() {
		/* @var Df_Cms_Model_Resource_Page_Version_Collection $collection */
		$collection = Df_Cms_Model_Page_Version::c();
		$collection
			->addPageFilter($this->getPage())
			->addVisibilityFilter(rm_admin_id(), Df_Cms_Model_Config::s()->getAllowedAccessLevel())
			->addUserColumn()
			->addUserNameColumn()
		;
		if (!$this->getParam($this->getVarNameSort())) {
			$collection->addNumberSort();
		}

		$this->setCollection($collection);
		return parent::_prepareCollection();
	}

	/**
	 * Retrieve collection for grid if there is not collection call _prepareCollection
	 * @return Df_Cms_Model_Resource_Page_Version_Collection
	 */
	public function getCollection()
	{
		if (!$this->_collection) {
			$this->_prepareCollection();
		}
		return $this->_collection;
	}

	/**
	 * Prepare versions grid columns
	 * @return Df_Cms_Block_Admin_Page_Edit_Tab_Versions
	 */
	protected function _prepareColumns()
	{
/*
		$this->addColumn('version_number', array(
			'header' => df_h()->cms()->__('Version #'),'width' => 100,'index' => 'version_number','type' => 'options','options' => df_h()->cms()->getVersionsArray($this->getPage())
		));
*/
		$this->addColumn('label', array(
			'header' => df_h()->cms()->__('Version Label'),'index' => 'label','type' => 'options','options' => $this->getCollection()
								->getAsArray('label', 'label')
		));
		$this->addColumn('owner', array(
			'header' => df_h()->cms()->__('Owner'),'index' => 'username','type' => 'options','options' => $this->getCollection()->getUsersArray(false),'width' => 250
		));
		$this->addColumn('access_level', array(
			'header' => df_h()->cms()->__('Access Level'),'index' => 'access_level','type' => 'options','width' => 100,'options' => df_h()->cms()->getVersionAccessLevels()
		));
		$this->addColumn('revisions', array(
			'header' => df_h()->cms()->__('Revisions Qty'),'index' => 'revisions_count','type' => 'number'
		));
		$this->addColumn('created_at', array(
			'width'	 => 150,'header'	=> df_h()->cms()->__('Created At'),'index'	 => 'created_at','type'	  => 'datetime',));
		return parent::_prepareColumns();
	}

	/**
	 * Prepare url for reload grid through ajax
	 * @return string
	 */
	public function getGridUrl()
	{
		return $this->getUrl('*/*/versions', array('_current'=>true));
	}

	/**
	 * Retrieve current page instance
	 * @return Df_Cms_Model_Page
	 */
	public function getPage()
	{
		return Mage::registry('cms_page');
	}

	/**
	 * Prepare label for tab
	 * @return string
	 */
	public function getTabLabel()
	{
		return df_h()->cms()->__('Versions');
	}

	/**
	 * Prepare title for tab
	 * @return string
	 */
	public function getTabTitle()
	{
		return df_h()->cms()->__('Versions');
	}

	/**
	 * Returns status flag about this tab can be shown or not
	 * @return true
	 */
	public function canShowTab()
	{
		return true;
	}

	/**
	 * Returns status flag about this tab hidden or not
	 * @return true
	 */
	public function isHidden()
	{
		return false;
	}

	/**
	 * @override
	 * @return Df_Cms_Block_Admin_Page_Edit_Tab_Versions
	 */
	protected function _prepareMassaction() {
		parent::_prepareMassaction();
		if (Df_Cms_Model_Config::s()->canCurrentUserDeleteVersion()) {
			$this->setMassactionIdField('version_id');
			$this->getMassactionBlock()->setFormFieldName('version');
			$this->getMassactionBlock()->addItem('delete', array(
				'label'	=> df_h()->cms()->__('Delete'),'url'	  => $this->getUrl('*/*/massDeleteVersions', array('_current' => true)),'confirm'  => df_h()->cms()->__('Are you sure?'),'selected' => true,));
		}
		return $this;
	}

	/**
	 * Grid row event edit url
	 * @return string
	 */
	public function getRowUrl($row)
	{
		return $this->getUrl('*/cms_page_version/edit', array('page_id' => $row->getPageId(), 'version_id' => $row->getVersionId()));
	}
}