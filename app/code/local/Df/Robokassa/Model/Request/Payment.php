<?php
/**
 * @method Df_Robokassa_Model_Payment getPaymentMethod()
 */
class Df_Robokassa_Model_Request_Payment extends Df_Payment_Model_Request_Payment {
	/**
	 * @override
	 * @return array(string => string|int|null)
	 */
	protected function getParamsInternal() {
		return
			array(
				self::REQUEST_VAR__SHOP_ID => $this->getShopId()
				,self::REQUEST_VAR__PAYMENT_AMOUNT => $this->getAmount()->getAsString()
				,self::REQUEST_VAR__ORDER_ID => $this->getOrder()->getIncrementId()
				,self::REQUEST_VAR__DESCRIPTION => $this->getPaymentDescription()
				,self::REQUEST_VAR__SIGNATURE =>	$this->getSignature()
				,self::REQUEST_VAR__CURRENCY =>
					$this->getPaymentMethod()->getRmConfig()->service()
						->getCurrencyCodeInServiceFormat()
				,self::REQUEST_VAR__EMAIL => null
			)
		;
	}

	/**
	 * @param Mage_Sales_Model_Order_Item $orderItem
	 * @return string
	 */
	private function getOrderItemDescription(Mage_Sales_Model_Order_Item $orderItem) {
		return
			rm_sprintf(
				self::TEMPLATE__ORDER_ITEM_DESCRIPTION
				,$orderItem->getName()
				,Df_Sales_Model_Order_Item_Extended::i($orderItem)->getQtyOrdered()
			)
		;
	}

	/** @return string[] */
	private function getOrderItemDescriptions() {
		if (!isset($this->{__METHOD__})) {
			/** @var string[] $result */
			$result = array();
			foreach ($this->getOrder()->getItemsCollection(array(), true) as $orderItem) {
				/** @var Mage_Sales_Model_Order_Item $orderItem */
				$result[]= $this->getOrderItemDescription($orderItem);
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return float */
	private function getPaymentAmount() {return round($this->getAmount()->getOriginal(), 2);}

	/** @return string */
	private function getPaymentDescription() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_concat_enum($this->getOrderItemDescriptions());
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getSignature() {
		return md5(implode(self::SIGNATURE_PARTS_SEPARATOR, $this->preprocessParams(array(
			self::REQUEST_VAR__SHOP_ID => $this->getShopId()
			,self::REQUEST_VAR__PAYMENT_AMOUNT => $this->getAmount()->getAsString()
			,self::REQUEST_VAR__ORDER_ID => $this->getOrder()->getIncrementId()
			,'dummy-1' => $this->getPaymentMethod()->getRmConfig()->service()->getRequestPassword()
		))));
	}
	const REQUEST_VAR__CURRENCY = 'sIncCurrLabel';
	const REQUEST_VAR__DESCRIPTION = 'Desc';
	const REQUEST_VAR__EMAIL = 'sEmail';
	const REQUEST_VAR__ORDER_ID = 'InvId';
	const REQUEST_VAR__PAYMENT_AMOUNT = 'OutSum';
	const REQUEST_VAR__SHOP_ID = 'MrchLogin';
	const REQUEST_VAR__SIGNATURE = 'SignatureValue';
	const SIGNATURE_PARTS_SEPARATOR = ':';
	const TEMPLATE__ORDER_ITEM_DESCRIPTION = '%s (%d)';
}