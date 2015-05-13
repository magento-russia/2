<?php
/**
 * Reward rate grid
 */
class Df_Reward_Block_Adminhtml_Reward_Rate_Grid extends Mage_Adminhtml_Block_Widget_Grid {
	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->setId('rewardRatesGrid');
	}

	/**
	 * Prepare grid collection object
	 * @return Df_Reward_Block_Adminhtml_Reward_Rate_Grid
	 */
	protected function _prepareCollection()
	{
		/* @var $collection Df_Reward_Model_Resource_Reward_Rate_Collection */
		$collection = Df_Reward_Model_Resource_Reward_Rate_Collection::i();
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}

	/**
	 * Prepare grid columns
	 * @return Df_Reward_Block_Adminhtml_Reward_Rate_Grid
	 */
	protected function _prepareColumns()
	{
		$this->addColumn('rate_id', array(
			'header' => df_h()->reward()->__('ID'),'align'  => 'left','index'  => 'rate_id','width'  => 1,));
		$this->addColumn('website_id', array(
			'header'  => df_h()->reward()->__('Website')
			,'index'   => 'website_id'
			,'type'	=> 'options'
			,'options' => Df_Reward_Model_Source_Website::s()->toOptionArray()
		));
		$this->addColumn('customer_group_id', array(
			'header' => df_h()->reward()->__('Customer Group')
			,'index' => 'customer_group_id'
			,'type' => 'options'
			,'options' => Df_Reward_Model_Source_Customer_Groups::I()->toOptionArray()
		));
		$this->addColumn('rate', array(
			'getter' => array($this, 'getRateText')
			,'header' => df_h()->reward()->__('Rate')
			,'filter' => false
			,'sortable' => false
			,'html_decorators' => 'nobr'
		));
		return parent::_prepareColumns();
	}

	/**
	 * Row click url
	 * @return string
	 */
	public function getRowUrl($row)
	{
		return $this->getUrl('*/*/edit', array('rate_id' => $row->getId()));
	}

	/**
	 * Rate text getter
	 *
	 * @param Varien_Object $row
	 * @return string|null
	 */
	public function getRateText($row)
	{
		$websiteId = $row->getWebsiteId();
		$result =
			Df_Reward_Model_Reward_Rate::getRateText(
				$row->getDirection()
				,$row->getPoints()
				,$row->getCurrencyAmount()
				,0 == $websiteId
				?
					null
				:
					Mage::app()->getWebsite($websiteId)->getBaseCurrencyCode()
		);
		return $result;
	}
}