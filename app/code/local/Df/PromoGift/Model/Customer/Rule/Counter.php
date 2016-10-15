<?php
class Df_PromoGift_Model_Customer_Rule_Counter extends Df_Core_Model {
	/** @return array */
	public function getGiftingQuoteItemIds() {
		/** @var array $result */
		$result = array();
		$dataContainer = $this->getDataContainer();
		/** @var array $dataContainer */
		df_assert_array($dataContainer);
		foreach ($dataContainer as $ruleData) {
			/** @var array $ruleData */
			df_assert_array($ruleData);
			$ruleQuoteItems= dfa($ruleData, self::KEY_RULE_QUOTE_ITEMS, array());
			df_assert_array($ruleQuoteItems);
			$result = array_merge ($result, array_values($ruleQuoteItems));
		}
		df_result_array($result);
		return $result;
	}

	/** @return Df_PromoGift_Model_Customer_Rule_Counter */
	public function reset() {
		$this->getSession()->unsetData(self::SESSION_CONTAINER_KEY);
		return $this;
	}

	/**
	 * @param int $ruleId
	 * @param int $quoteItemId
	 * @return Df_PromoGift_Model_Customer_Rule_Counter
	 */
	public function count($ruleId, $quoteItemId) {
		df_param_integer($ruleId, 0);
		df_param_integer($quoteItemId, 1);
		$counterValue = $this->getCounterValue($ruleId);
		/** @var int $counterValue */
		df_assert_integer($counterValue);
		$counterValue++;
		$ruleData = $this->getRuleData($ruleId);
		df_assert_array($ruleData);
		$ruleData[self::KEY_RULE_COUNTER] = $counterValue;
		/**
		 * Учитываем подарочный товар
		 */
		$ruleQuoteItems = dfa($ruleData, self::KEY_RULE_QUOTE_ITEMS, array());
		$ruleQuoteItems[]= $quoteItemId;
		$ruleData[self::KEY_RULE_QUOTE_ITEMS] = $ruleQuoteItems;
		$ruleData[self::KEY_RULE_COUNTER] = $counterValue;
		$this->setRuleData($ruleId, $ruleData);
		return $this;
	}

	/**
	 * @param int $ruleId
	 * @return int
	 */
	public function getCounterValue($ruleId) {
		df_param_integer($ruleId, 0);
		$ruleData = $this->getRuleData($ruleId);
		df_assert_array($ruleData);
		/** @var int $result */
		$result = dfa($ruleData, self::KEY_RULE_COUNTER, 0);
		df_result_integer($result);
		return $result;
	}

	/**
	 * @param int $ruleId
	 * @return array
	 */
	private function getRuleData($ruleId) {
		$dataContainer = $this->getDataContainer();
		/** @var array $dataContainer */
		df_assert_array($dataContainer);
		$result = dfa($dataContainer, $ruleId);
		if (is_null($result)) {
			$result = array();
			$dataContainer[$ruleId] = $result;
			$this->setDataContainer($dataContainer);
		}
		df_result_array($result);
		return $result;
	}

	/**
	 * @param int $ruleId
	 * @param array $ruleData
	 * @return Df_PromoGift_Model_Customer_Rule_Counter
	 */
	private function setRuleData($ruleId, array $ruleData) {
		df_param_integer($ruleId, 0);
		df_param_array($ruleData, 1);
		$dataContainer = $this->getDataContainer();
		/** @var array $dataContainer */
		df_assert_array($dataContainer);
		$dataContainer[$ruleId] = $ruleData;
		$this->setDataContainer($dataContainer);
		return $this;
	}

	/** @return array */
	private function getDataContainer() {
		$result = $this->getSession()->getData(self::SESSION_CONTAINER_KEY);
		if (is_null($result)) {
			$result = array();
			$this->setDataContainer($result);
		}
		df_result_array($result);
		return $result;
	}

	/**
	 * @param array $dataContainer
	 * @return Df_PromoGift_Model_Customer_Rule_Counter
	 */
	private function setDataContainer(array $dataContainer) {
		$this->getSession()->setData(self::SESSION_CONTAINER_KEY, $dataContainer);
		return $this;
	}

	/** @return Mage_Customer_Model_Session */
	private function getSession() {
		return df_session_customer();
	}


	const SESSION_CONTAINER_KEY = 'promo_gift_counter';
	const KEY_RULE_COUNTER = 'counter';
	const KEY_RULE_QUOTE_ITEMS = 'quote_items';
	/** @return Df_PromoGift_Model_Customer_Rule_Counter */
	public static function i() {return new self;}
}