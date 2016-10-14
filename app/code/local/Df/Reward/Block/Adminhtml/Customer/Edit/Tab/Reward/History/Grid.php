<?php
class Df_Reward_Block_Adminhtml_Customer_Edit_Tab_Reward_History_Grid
	extends Mage_Adminhtml_Block_Widget_Grid {
	/**
	 * Return grid url for ajax actions
	 * @return string
	 */
	public function getGridUrl() {
		return $this->getUrl('*/*/historyGrid', array('_current' => true));
	}

	/**
	 * Add column filter to collection
	 *
	 * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
	 * @return Df_Reward_Block_Adminhtml_Customer_Edit_Tab_Reward_History_Grid
	 */
	protected function _addColumnFilterToCollection($column)
	{
		if ($this->getCollection()) {
			$field = ( $column->getFilterIndex()) ? $column->getFilterIndex() : $column->getIndex();
			if (($field === 'website_id') || ($field === 'points_balance')) {
				$cond = $column->getFilter()->getCondition();
				if ($field && isset($cond)) {
					$this->getCollection()->addFieldToFilter('main_table.'.$field , $cond);
				}
			} else {
				parent::_addColumnFilterToCollection($column);
			}
		}
		return $this;
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->setUseAjax(true);
		$this->setId('rewardPointsHistoryGrid');
	}

	/**
	 * Prepare grid collection object
	 * @return Df_Reward_Block_Adminhtml_Customer_Edit_Tab_Reward_History_Grid
	 */
	protected function _prepareCollection()
	{
		/* @var $collection Df_Reward_Model_Resource_Reward_History_Collection */
		$collection =
			Df_Reward_Model_Reward_History::c()
				->addCustomerFilter($this[self::$P__CUSTOMER_ID])
				->setExpiryConfig(df_h()->reward()->getExpiryConfig())
				->addExpirationDate()
				->setOrder('history_id', 'desc')
		;
		$collection->setDefaultOrder();
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}

	/**
	 * Prepare grid columns
	 * @return Mage_Widget_Block_Adminhtml_Widget_Instance_Grid
	 */
	protected function _prepareColumns() {
		$this->addColumn('points_balance', array(
			'type' => 'number'
			,'index' => 'points_balance'
			,'header' => df_h()->reward()->__('Balance')
			,'sortable' => false
			,'filter' => false
			,'width' => 1
		));
		$this->addColumn('currency_amount', array(
			'type' => 'currency'
			,'currency' => 'base_currency_code'
			,'rate' => 1
			,'index' => 'currency_amount'
			,'header' => df_h()->reward()->__('Amount Balance')
			,'sortable' => false
			,'filter' => false
			,'width' => 1
		));
		$this->addColumn('points_delta', array(
			'type' => 'number'
			,'index' => 'points_delta'
			,'header' => df_h()->reward()->__('Points Change')
			,'sortable' => false
			,'filter' => false
			,'show_number_sign' => true
			,'width' => 1
		));
		$this->addColumn('currency_delta', array(
			'type' => 'currency'
			,'currency' => 'base_currency_code'
			,'rate' => 1
			,'index' => 'currency_delta'
			,'header' => df_h()->reward()->__('Amount')
			,'sortable' => false
			,'filter' => false
			,'show_number_sign' => true
			,'width' => 1
		));
		$this->addColumn('rate', array(
			'getter' => 'getRateText'
			,'header' => df_h()->reward()->__('Rate')
			,'sortable' => false
			,'filter' => false
		));
		// TODO: instead of source models move options to a getter
		$this->addColumn('website', array(
			'type' => 'options'
			,'options' => Df_Reward_Model_Source_Website::s()->toOptionArray(false)
			,'index' => 'website_id'
			,'header' => df_h()->reward()->__('Website')
			,'sortable' => false
		));
// TODO: custom renderer for reason, which includes comments
		$this->addColumn('message', array(
			'index'	=> 'message'
			,'getter' => 'getMessage'
			,'header' => df_h()->reward()->__('Reason')
			,'sortable' => false
			,'filter' => false
			,'align' => 'left'
		));
		$this->addColumn('created_at', array(
			'type' => 'datetime'
			,'index' => 'created_at'
			,'header' => df_h()->reward()->__('Created At')
			,'sortable' => false
			,'align' => 'left'
			,'html_decorators' => 'nobr',));
		$this->addColumn('expiration_date', array(
			'type' => 'datetime'
			,'getter' => 'getExpiresAt'
			,'header' => df_h()->reward()->__('Expires At')
			,'sortable' => false
			,'filter'  => false, // needs custom filter
			'align'	=> 'left'
			,'html_decorators' => 'nobr'
		));
		// TODO: merge with reason
		$this->addColumn('comment', array(
			'index'	=> 'comment'
			,'header' => df_h()->reward()->__('Comment')
			,'sortable' => false
			,'filter' => false
			,'align' => 'left'
		));
		return parent::_prepareColumns();
	}

	/** @var string */
	private static $P__CUSTOMER_ID = 'customer_id';

	/**
	 * @used-by Df_Reward_Block_Adminhtml_Customer_Edit_Tab_Reward_History::_prepareLayout()
	 * @used-by Df_Reward_Adminhtml_Customer_RewardController::historyGridAction()
	 * @param int $customerId
	 * @return Df_Reward_Block_Adminhtml_Customer_Edit_Tab_Reward_History_Grid
	 */
	public static function i($customerId) {
		return df_block_l(new self(array(self::$P__CUSTOMER_ID => $customerId)));
	}
}