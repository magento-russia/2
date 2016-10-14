<?php
class Df_CustomerBalance_Block_Adminhtml_Customer_Edit_Tab_Customerbalance_Balance_Grid
	extends Mage_Adminhtml_Block_Widget_Grid {
	/**
	 * @override
	 * @return Df_CustomerBalance_Block_Adminhtml_Customer_Edit_Tab_Customerbalance_Balance_Grid
	 */
	protected function _prepareCollection() {
		/** @var Df_CustomerBalance_Model_Resource_Balance_Collection $collection */
		$collection = Df_CustomerBalance_Model_Balance::c();
		$collection->addFieldToFilter('customer_id', $this->getRequest()->getParam('id'));
		$this->setCollection($collection);
		parent::_prepareCollection();
		return $this;
	}

	/**
	 * @override
	 * @return Df_CustomerBalance_Block_Adminhtml_Customer_Edit_Tab_Customerbalance_Balance_Grid
	 */
	protected function _prepareColumns() {
		$this
			->addColumn(
				'amount'
				,array(
					'header' => Df_CustomerBalance_Helper_Data::s()->__('Balance')
					,'width' => 50
					,'index' => 'amount'
					,'sortable' => false
					,'renderer' =>
						'df_customerbalance/adminhtml_widget_grid_column_renderer_currency'
				)
			)
			->addColumn(
				'website_id'
				,array(
					'header' => Df_CustomerBalance_Helper_Data::s()->__('Website')
					,'index' => 'website_id'
					,'sortable' => false
					,'type' => 'options'
					,'options' =>
						df_mage()->adminhtml()->system()->storeSingleton()
							->getWebsiteOptionHash()
				)
			)
		;
		parent::_prepareColumns();
		return $this;
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->setId('balanceGrid');
		$this->setUseAjax(true);
		$this->setDefaultSort('name');
		$this->setFilterVisibility(false);
		$this->setPagerVisibility(false);
	}
}