<?php
class Df_CustomerBalance_Block_Account_Balance extends Df_Core_Block_Template_NoCache {
	/** @return float */
	public function getBalance() {
		/** @var float $result */
		$result = 0.0;
		$customerId = df_session_customer()->getCustomerId();
		if ($customerId) {
			/** @var Df_CustomerBalance_Model_Balance $balance */
			$balance = Df_CustomerBalance_Model_Balance::i();
			$balance->setCustomerId($customerId);
			$balance->loadByCustomer();
			$result = $balance->getAmount();
		}
		return $result;
	}
}