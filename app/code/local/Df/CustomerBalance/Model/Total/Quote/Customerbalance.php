<?php
class Df_CustomerBalance_Model_Total_Quote_Customerbalance
	extends Mage_Sales_Model_Quote_Address_Total_Abstract {
	/**
	 * Обратите внимание, что родительский класс Mage_Sales_Model_Quote_Address_Total_Abstract
	 * не является потомком класса Varien_Object,
	 * поэтому у нашего класса нет метода _construct,
	 * и мы перекрываем именно конструктор
	 * @override
	 * @return Df_CustomerBalance_Model_Total_Quote_Customerbalance
	 */
	public function __construct() {$this->setCode('customerbalance');}

	/**
	 * Collect customer balance totals for specified address
	 * @param Mage_Sales_Model_Quote_Address|Df_Sales_Model_Quote_Address $address
	 * @return Df_CustomerBalance_Model_Total_Quote_Customerbalance
	 */
	public function collect(Mage_Sales_Model_Quote_Address $address) {
		if (!Df_CustomerBalance_Helper_Data::s()->isEnabled()) {
			return $this;
		}
		$quote = $address->getQuote();
		if (!$quote->getCustomerBalanceCollected()) {
			$quote->setBaseCustomerBalanceAmountUsed(0);
			$quote->setCustomerBalanceAmountUsed(0);
			$quote->setCustomerBalanceCollected(true);
		}
		$baseTotalUsed = $totalUsed = $baseUsed = $used = 0;
		$baseBalance = $balance = 0;
		if ($quote->getCustomer()->getId()) {
			if ($quote->getUseCustomerBalance()) {
				$store = rm_store($quote->getStoreId());
				$baseBalance = Df_CustomerBalance_Model_Balance::i()
					->setCustomer($quote->getCustomer())
					->setWebsiteId($store->getWebsiteId())
					->loadByCustomer()
					->getAmount();
				$balance = $quote->getStore()->convertPrice($baseBalance);
			}
		}

		$baseAmountLeft = $baseBalance - $quote->getBaseCustomerBalanceAmountUsed();
		$amountLeft = $balance - $quote->getCustomerBalanceAmountUsed();
		if ($baseAmountLeft >= $address->getBaseGrandTotal()) {
			$baseUsed = $address->getBaseGrandTotal();
			$used = $address->getGrandTotal();
			$address->setBaseGrandTotal(0);
			$address->setGrandTotal(0);
		} else {
			$baseUsed = $baseAmountLeft;
			$used = $amountLeft;
			$address->setBaseGrandTotal($address->getBaseGrandTotal()-$baseAmountLeft);
			$address->setGrandTotal($address->getGrandTotal()-$amountLeft);
		}

		$baseTotalUsed = $quote->getBaseCustomerBalanceAmountUsed() + $baseUsed;
		$totalUsed = $quote->getCustomerBalanceAmountUsed() + $used;
		$quote->setBaseCustomerBalanceAmountUsed($baseTotalUsed);
		$quote->setCustomerBalanceAmountUsed($totalUsed);
		$address->setBaseCustomerBalanceAmount($baseUsed);
		$address->setCustomerBalanceAmount($used);
		return $this;
	}

	/**
	 * Return shopping cart total row items
	 * @param Mage_Sales_Model_Quote_Address|Df_Sales_Model_Quote_Address $address
	 * @return Df_CustomerBalance_Model_Total_Quote_Customerbalance
	 */
	public function fetch(Mage_Sales_Model_Quote_Address $address) {
		if (!Df_CustomerBalance_Helper_Data::s()->isEnabled()) {
			return $this;
		}
		if ($address->getCustomerBalanceAmount()) {
			$address->addTotal(array(
				'code' => $this->getCode()
				,'title' => Df_CustomerBalance_Helper_Data::s()->__('Store Credit')
				,'value' => -$address->getCustomerBalanceAmount()
			));
		}
		return $this;
	}
}