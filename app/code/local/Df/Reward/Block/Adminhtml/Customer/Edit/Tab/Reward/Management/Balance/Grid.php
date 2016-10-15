<?php
class Df_Reward_Block_Adminhtml_Customer_Edit_Tab_Reward_Management_Balance_Grid
	extends Mage_Adminhtml_Block_Widget_Grid {
	/** @return string */
	public function getDeleteOrphanPointsUrl() {
		return $this->getUrl('*/customer_reward/deleteOrphanPoints', array('_current' => true));
	}

	/** @return Df_Customer_Model_Customer */
	public function getCustomer() {return Mage::registry('current_customer');}

	/**
	 * @override
	 * @return Df_Reward_Block_Adminhtml_Customer_Edit_Tab_Reward_Management_Balance_Grid
	 */
	protected function _afterLoadCollection() {
		parent::_afterLoadCollection();
		/* @var $item Df_Reward_Model_Reward */
		foreach ($this->getCollection() as $item) {
			$website = $item->getData('website_id');
			if ($website !== null) {
				$minBalance = df_h()->reward()->getGeneralConfig('min_points_balance', (int)$website);
				$maxBalance = df_h()->reward()->getGeneralConfig('max_points_balance', (int)$website);
				$item->addData(array(
					'min_points_balance' => df_nat0($minBalance)
					,'max_points_balance' =>
						! df_nat0($maxBalance)
						? df_mage()->adminhtmlHelper()->__('Unlimited')
						: $maxBalance
				));
			}
			else {
				$this->_customerHasOrphanPoints = true;
				$item->addData(array(
					'min_points_balance' => df_mage()->adminhtmlHelper()->__('No Data')
					,'max_points_balance' => df_mage()->adminhtmlHelper()->__('No Data')
				));
			}
			$item->setCustomer($this->getCustomer());
		}
		return $this;
	}

	/**
	 * Add button to delete orphan points if customer has such points
	 * @override
	 * @param string $html
	 * @return  string
	 */
	protected function _afterToHtml($html) {
		$html = parent::_afterToHtml($html);
		if ($this->_customerHasOrphanPoints) {
			$html .= df_admin_button(array(
				'label' => df_h()->reward()->__('Delete Orphan Points')
				,'onclick' => df_admin_button_location($this->getDeleteOrphanPointsUrl())
				,'class' => 'scalable delete'
			));
		}
		return $html;
	}

	/**
	 * Prepare grid collection
	 * @override
	 * @return Df_Reward_Block_Adminhtml_Customer_Edit_Tab_Reward_Management_Balance_Grid
	 */
	protected function _prepareCollection() {
		/** @var Df_Reward_Model_Resource_Reward_Collection $collection */
		$collection = Df_Reward_Model_Reward::c();
		$collection->addFieldToFilter('customer_id', $this->getCustomer()->getId());
		$this->setCollection($collection);
		parent::_prepareCollection();
		return $this;
	}

	/**
	 * @override
	 * @return Df_Reward_Block_Adminhtml_Customer_Edit_Tab_Reward_Management_Balance_Grid
	 */
	protected function _prepareColumns() {
		$this
			->addColumn(
				'website_id'
				,array(
					'header' => df_h()->reward()->__('Website')
					,'index' => 'website_id','sortable' => false
					,'type' => 'options'
					,'options' => Df_Reward_Model_Source_Website::s()->toOptionArray(false)
				)
			)
			->addColumn(
				'points_balance'
				,array(
					'header' => df_h()->reward()->__('Balance')
					,'index' => 'points_balance'
					,'sortable' => false
					,'align' => 'center'
				)
			)
			->addColumn(
				'currency_amount'
				,array(
					'header' => df_h()->reward()->__('Currency Amount')
					,'getter' => 'getFormatedCurrencyAmount'
					,'align' => 'right'
					,'sortable' => false
				)
			)
			->addColumn(
				'min_balance'
				,array(
					'header' =>
						df_h()->reward()->__(
							'Minimum Reward Points Balance to be able to Redeem'
						)
						,'index' => 'min_points_balance'
						,'sortable' => false
						,'align' => 'center'
				)
			)
			->addColumn(
				'max_balance'
				,array(
					'header' => df_h()->reward()->__('Cap Reward Points Balance at')
					,'index' => 'max_points_balance'
					,'sortable' => false
					,'align' => 'center'
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
		$this->setId('rewardPointsBalanceGrid');
		$this->setUseAjax(true);
		$this->setFilterVisibility(false);
		$this->setPagerVisibility(false);
	}
	/** @var bool */
	protected $_customerHasOrphanPoints = false;
}