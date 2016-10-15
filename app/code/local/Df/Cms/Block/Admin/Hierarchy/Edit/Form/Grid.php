<?php
class Df_Cms_Block_Admin_Hierarchy_Edit_Form_Grid extends Mage_Adminhtml_Block_Widget_Grid {
	/**
	 * @override
	 * @return Df_Cms_Block_Admin_Hierarchy_Edit_Form_Grid
	 */
	protected function _prepareCollection() {
		/** @var Df_Cms_Model_Resource_Page_Collection $collection */
		$collection = Df_Cms_Model_Page::c();
		$store = $this->_getStore();
		if ($store->getId()) {
			$collection->addStoreFilter($store);
		}
		$this->setCollection($collection);
		parent::_prepareCollection();
		return $this;
	}

	/**
	 * @override
	 * @return Df_Cms_Block_Admin_Hierarchy_Edit_Form_Grid
	 */
	protected function _prepareColumns() {
		$this
			->addColumn(
				'is_selected'
				,array(
					'header_css_class' => 'a-center'
					,'type' => 'checkbox'
					,'align' => 'center'
					,'index' => 'page_id'
					,'filter' => false
				)
			)
			->addColumn(
				'page_id'
				,array(
					'header' => df_h()->cms()->__('Page ID')
					,'sortable' => true
					,'width' => '60px'
					,'type' => 'range'
					,'index' => 'page_id'
				)
			)
			->addColumn(
				'title'
				,array(
					'header' => df_h()->cms()->__('Title')
					,'index' => 'title'
					,'column_css_class' => 'label'
				)
			)
			->addColumn(
				'identifier'
				,array(
					'header' => df_h()->cms()->__('URL Key')
					,'index' => 'identifier'
					,'column_css_class' => 'identifier'
				)
			)
		;
		parent::_prepareColumns();
		return $this;
	}

	/**
	 * @override
	 * @return string
	 */
	public function getGridUrl() {
		return $this->getUrl('*/*/pageGrid', array('_current' => true));
	}

	/**
	 * Get selected store by store id passed through query.
	 * @return Df_Core_Model_StoreM
	 */
	protected function _getStore() {
		return df_store(df_nat0($this->getRequest()->getParam('store', 0)));
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->setRowClickCallback('hierarchyNodes.pageGridRowClick.bind(hierarchyNodes)');
		$this->setDefaultSort('page_id');
		$this->setMassactionIdField('page_id');
		$this->setUseAjax(true);
		$this->setId('cms_page_grid');
	}
}