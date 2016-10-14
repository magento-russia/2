<?php
class Df_Cms_Block_Admin_Page_Version_Edit_Revisions extends Mage_Adminhtml_Block_Widget_Grid {
	/**
	 * @override
	 * @return Df_Cms_Model_Resource_Page_Version_Collection
	 */
	public function getCollection() {
		if (!$this->_collection) {
			$this->_prepareCollection();
		}
		return $this->_collection;
	}

	/**
	 * @override
	 * @return string
	 */
	public function getGridUrl() {
		return $this->getUrl('*/*/revisions', array('_current'=>true));
	}

	/** @return Mage_Cms_Model_Page */
	public function getPage() {
		return Mage::registry('cms_page');
	}

	/**
	 * @override
	 * @param Df_Cms_Model_Page_Revision $row
	 * @return string
	 */
	public function getRowUrl($row) {
		return
			$this->getUrl(
				'*/cms_page_revision/edit'
				,array(
					'page_id' => $row->getPageId()
					,'revision_id' => $row->getRevisionId()
				)
			)
		;
	}

	/** @return Df_Cms_Model_Page_Version */
	public function getVersion() {
		return Mage::registry('cms_page_version');
	}

	/**
	 * @override
	 * @return Df_Cms_Block_Admin_Page_Version_Edit_Revisions
	 */
	protected function _prepareCollection() {
		/** @var Df_Cms_Model_Resource_Page_Revision_Collection $collection */
		$collection = Df_Cms_Model_Page_Revision::c();
		$collection->addPageFilter($this->getPage());
		$collection->addVersionFilter($this->getVersion());
		$collection->addUserColumn();
		$collection->addUserNameColumn();
		$this->setCollection($collection);
		parent::_prepareCollection();
		return $this;
	}

	/**
	 * @override
	 * @return Df_Cms_Block_Admin_Page_Version_Edit_Revisions
	 */
	protected function _prepareColumns() {
		$this
			->addColumn(
				'revision_number'
				,array(
					'header' => df_h()->cms()->__('Revision #')
					,'width' => 200
					,'type' => 'number'
					,'index' => 'revision_number'
				)
			)
			->addColumn(
				'created_at'
				,array(
					'header' => df_h()->cms()->__('Created')
					,'index' => 'created_at'
					,'type' => 'datetime'
					,'filter_time' => true
					,'width' => 250
				)
			)
			->addColumn(
				'author'
				,array(
					'header' => df_h()->cms()->__('Author')
					,'index' => 'user'
					,'type' => 'options'
					,'options' => $this->getCollection()->getUsersArray()
				)
			)
		;
		parent::_prepareColumns();
		return $this;
	}

	/**
	 * @override
	 * @return Df_Cms_Block_Admin_Page_Version_Edit_Revisions
	 */
	protected function _prepareMassaction() {
		parent::_prepareMassaction();
		if (Df_Cms_Model_Config::s()->canCurrentUserDeleteRevision()) {
			$this->setMassactionIdField('revision_id');
			$this->getMassactionBlock()->setFormFieldName('revision');
			$this->getMassactionBlock()
				->addItem(
					'delete'
					,array(
						'label'=> df_h()->cms()->__('Delete')
						,'url' =>
							$this->getUrl(
								'*/*/massDeleteRevisions'
								,array('_current' => true)
							)
						,'confirm' => df_h()->cms()->__('Are you sure?')
					)
				)
			;
		}
		return $this;
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->setId('revisionsGrid');
		$this->setDefaultSort('revision_number');
		$this->setDefaultDir('DESC');
		$this->setUseAjax(true);
	}
}