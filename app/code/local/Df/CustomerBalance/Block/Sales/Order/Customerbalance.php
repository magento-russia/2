<?php
class Df_CustomerBalance_Block_Sales_Order_Customerbalance extends Df_Core_Block_Template_NoCache {
	/**
	 * Retrieve current order model instance
	 * @return Df_Sales_Model_Order
	 */
	public function getOrder() {return $this->getParentBlock()->getOrder();}

	public function getSource() {return $this->getParentBlock()->getSource();}

	/**
	 * Initialize customer balance order total
	 * @return Df_CustomerBalance_Block_Sales_Order_Customerbalance
	 */
	public function initTotals()
	{
		if ((float)$this->getSource()->getCustomerBalanceAmount() == 0) {
			return $this;
		}
		$total = new Varien_Object(array(
			'code'	  => $this->getNameInLayout(),'block_name'=> $this->getNameInLayout(),'area'	  => $this->getArea()
		));
		$after = $this->getAfterTotal();
		if (!$after) {
			$after = 'giftcards';
		}
		$this->getParentBlock()->addTotal($total, $after);
		return $this;
	}

	public function getLabelProperties()
	{
		return $this->getParentBlock()->getLabelProperties();
	}

	public function getValueProperties()
	{
		return $this->getParentBlock()->getValueProperties();
	}
}