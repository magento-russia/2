<?php
class Df_CustomerBalance_Block_Adminhtml_Customer_Edit_Tab_Customerbalance_Balance_History_Grid
	extends Mage_Adminhtml_Block_Widget_Grid {
	/**
	 * @override
	 * @return string
	 */
	public function getGridUrl() {
		return $this->getUrl('*/*/gridHistory', array('_current'=> true));
	}

	/**
	 * @override
	 * @return Df_CustomerBalance_Block_Adminhtml_Customer_Edit_Tab_Customerbalance_Balance_History_Grid
	 */
	protected function _prepareCollection() {
		/** @var Df_CustomerBalance_Model_Resource_Balance_History_Collection $collection */
		$collection = Df_CustomerBalance_Model_Balance_History::c();
		$collection->addFieldToFilter('customer_id', $this->getRequest()->getParam('id'));
		$this->setCollection($collection);
		parent::_prepareCollection();
		return $this;
	}

	/**
	 * @override
	 * @return Df_CustomerBalance_Block_Adminhtml_Customer_Edit_Tab_Customerbalance_Balance_History_Grid
	 */
	protected function _prepareColumns() {
		$this
			->addColumn(
				'updated_at'
				,array(
					'header' => df_h()->customer()->balance()->__('Date')
					,'index' => 'updated_at'
					,'type' => 'datetime'
					,'filter' => false
					,'width' => 200
				)
			)
			->addColumn(
				'website_id'
				,array(
					'header' => df_h()->customer()->balance()->__('Website')
					,'index' => 'website_id'
					,'type'	 => 'options'
					,'options' =>
						df_mage()->adminhtml()->system()->storeSingleton()
							->getWebsiteOptionHash()
					,'sortable'  => false
					,'width' => 200
				)
			)
			->addColumn(
				'balance_action'
				,array(
					'header' => df_h()->customer()->balance()->__('Action')
					,'width' => 70
					,'index' => 'action'
					,'sortable' => false
					,'type'	=> 'options'
					,'options' => Df_CustomerBalance_Model_Balance_History::s()->getActionNamesArray()
				)
			)
			->addColumn(
				'balance_delta'
				,array(
					'header' => df_h()->customer()->balance()->__('Balance Change')
					,'width' => 50
					,'index' => 'balance_delta'
					,'type' => 'price'
					,'sortable' => false
					,'filter' => false
					,'renderer' => 'df_customerbalance/adminhtml_widget_grid_column_renderer_currency'
				)
			)
			->addColumn(
				'balance_amount'
				,array(
					'header' => df_h()->customer()->balance()->__('Balance')
					,'width' => 50
					,'index' => 'balance_amount'
					,'sortable' => false
					,'filter' => false
					,'renderer' => 'df_customerbalance/adminhtml_widget_grid_column_renderer_currency'
				)
			)
			->addColumn(
				'is_customer_notified'
				,array(
					'header' => df_h()->customer()->balance()->__('Customer notified?')
					,'index' => 'is_customer_notified'
					,'type'	=> 'options'
					,'options'=>
						array(
							'1' => df_h()->customer()->balance()->__('Notified')
							,'0' => df_h()->customer()->balance()->__('No')
						)
					,'sortable' => false
					,'filter' => false
					,'width' => 75
				)
			)
			->addColumn(
				'additional_info'
				,array(
					'header' => df_h()->customer()->balance()->__('Additional information')
					,'index' => 'additional_info'
					,'sortable' => false
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
		$this->setId('historyGrid');
		$this->setUseAjax(true);
		$this->setDefaultSort('updated_at');
	}

	/**
	 * @param string|null $name [optional]
	 * @return Df_CustomerBalance_Block_Adminhtml_Customer_Edit_Tab_Customerbalance_Balance_History_Grid
	 */
	public static function i($name = null) {return df_block(__CLASS__, $name);}
}