<?php
/** @method Df_Robokassa_Model_Payment getMethod() */
class Df_Robokassa_Model_Request_Payment extends Df_Payment_Model_Request_Payment {
	/**
	 * @override
	 * @see Df_Payment_Model_Request_Payment::_params()
	 * @used-by Df_Payment_Model_Request_Payment::params()
	 * @return array(string => string|int)
	 */
	protected function _params() {return [
		'MerchantLogin' => $this->shopId()
		,'OutSum' => $this->amountS()
		,'InvId' => $this->orderIId()
		,'InvDesc' => $this->getPaymentDescription()
		,'IsTest' => 1
		,'SignatureValue' => $this->signature()
		,'IncCurrLabel' => $this->configS()->getCurrencyCodeInServiceFormat()
		,'Email' => null
	];}

	/**
	 * @param Mage_Sales_Model_Order_Item $orderItem
	 * @return string
	 */
	private function getOrderItemDescription(Mage_Sales_Model_Order_Item $orderItem) {return sprintf(
		'%s (%d)'
		,$orderItem->getName()
		,Df_Sales_Model_Order_Item_Extended::i($orderItem)->getQtyOrdered()
	);}

	/** @return string */
	private function getPaymentDescription() {
		if (!isset($this->{__METHOD__})) {
			/** @var string[] $resultA */
			$resultA = [];
			foreach ($this->order()->getItemsCollection(array(), true) as $orderItem) {
				/** @var Mage_Sales_Model_Order_Item $orderItem */
				$resultA[]= $this->getOrderItemDescription($orderItem);
			}
			$this->{__METHOD__} = df_csv_pretty($resultA);
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function signature() {return
		md5(implode(':', $this->preprocessParams([
			'MerchantLogin' => $this->shopId()
			,'OutSum' => $this->amountS()
			,'InvId' => $this->orderIId()
			,'dummy-1' => $this->password()
		])))
	;}
}