<?php
/** @method Df_Robokassa_Model_Payment getMethod() */
class Df_Robokassa_Model_Request_Payment extends Df_Payment_Model_Request_Payment {
	/**
	 * @override
	 * @see Df_Payment_Model_Request_Payment::_params()
	 * @used-by Df_Payment_Model_Request_Payment::params()
	 * @return array(string => string|int)
	 */
	protected function _params() {
		return array(
			self::REQUEST_VAR__SHOP_ID => $this->shopId()
			,self::REQUEST_VAR__PAYMENT_AMOUNT => $this->amountS()
			,self::REQUEST_VAR__ORDER_ID => $this->orderIId()
			,self::REQUEST_VAR__DESCRIPTION => $this->getPaymentDescription()
			,self::REQUEST_VAR__SIGNATURE => $this->getSignature()
			,self::REQUEST_VAR__CURRENCY => $this->configS()->getCurrencyCodeInServiceFormat()
			,self::REQUEST_VAR__EMAIL => null
		);
	}

	/**
	 * @param Mage_Sales_Model_Order_Item $orderItem
	 * @return string
	 */
	private function getOrderItemDescription(Mage_Sales_Model_Order_Item $orderItem) {
		return sprintf(
			'%s (%d)'
			,$orderItem->getName()
			,Df_Sales_Model_Order_Item_Extended::i($orderItem)->getQtyOrdered()
		);
	}

	/** @return string[] */
	private function getOrderItemDescriptions() {
		if (!isset($this->{__METHOD__})) {
			/** @var string[] $result */
			$result = array();
			foreach ($this->order()->getItemsCollection(array(), true) as $orderItem) {
				/** @var Mage_Sales_Model_Order_Item $orderItem */
				$result[]= $this->getOrderItemDescription($orderItem);
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return float */
	private function getPaymentAmount() {return round($this->amount()->getOriginal(), 2);}

	/** @return string */
	private function getPaymentDescription() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_csv_pretty($this->getOrderItemDescriptions());
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getSignature() {
		return md5(implode(self::SIGNATURE_PARTS_SEPARATOR, $this->preprocessParams(
			array(
			self::REQUEST_VAR__SHOP_ID => $this->shopId()
			,self::REQUEST_VAR__PAYMENT_AMOUNT => $this->amountS()
			,self::REQUEST_VAR__ORDER_ID => $this->orderIId()
			,'dummy-1' => $this->password()
			)
		)));
	}

	/**
	 * @used-by _params()
	 * @used-by getSignature()
	 * @return array(string => string|int)
	 */
	private function paramsForSignature() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = array(
				self::REQUEST_VAR__SHOP_ID => $this->shopId()
				,self::REQUEST_VAR__PAYMENT_AMOUNT => $this->amountS()
				,self::REQUEST_VAR__ORDER_ID => $this->orderIId()
			);
		}
		return $this->{__METHOD__};
	}

	const REQUEST_VAR__CURRENCY = 'sIncCurrLabel';
	const REQUEST_VAR__DESCRIPTION = 'Desc';
	const REQUEST_VAR__EMAIL = 'sEmail';
	const REQUEST_VAR__ORDER_ID = 'InvId';
	const REQUEST_VAR__PAYMENT_AMOUNT = 'OutSum';
	const REQUEST_VAR__SHOP_ID = 'MrchLogin';
	const REQUEST_VAR__SIGNATURE = 'SignatureValue';
	const SIGNATURE_PARTS_SEPARATOR = ':';
}