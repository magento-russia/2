<?php
class Df_Reward_Model_Resource_Reward extends Df_Core_Model_Resource {
	/**
	 * Delete orphan (points of deleted website) points by given customer
	 * @param integer $customerId
	 * @return Df_Reward_Model_Resource_Reward
	 */
	public function deleteOrphanPointsByCustomer($customerId) {
		if ($customerId) {
			$this->_getWriteAdapter()->delete(
				$this->getMainTable()
				,df_db_quote_into('customer_id = ?', $customerId) . ' AND `website_id` IS null'
			);
		}
		return $this;
	}

	/**
	 * Retrieve reward salesrule data by given rule Id or array of Ids
	 * @param integer | array $rule
	 * @return array
	 */
	public function getRewardSalesrule($rule) {
		$data = array();
		$select = df_select()->from(df_table('df_reward/reward_salesrule'));
		if (is_array($rule)) {
			$select->where('rule_id IN (?)', $rule);
			$data = df_conn()->fetchAll($select);
		} else if ($rule) {
			$select->where('rule_id = ?', (int)$rule);
			$data = df_conn()->fetchRow($select);
		}
		return $data;
	}

	/**
	 * Fetch reward by customer and website and set data to reward object
	 * @param Df_Reward_Model_Reward $reward
	 * @param integer $customerId
	 * @param integer $websiteId
	 * @return Df_Reward_Model_Resource_Reward
	 */
	public function loadByCustomerId(Df_Reward_Model_Reward $reward, $customerId, $websiteId) {
		$select = df_select()
			->from($this->getMainTable())
			->where('customer_id = ?', $customerId)
			->where('website_id = ?', $websiteId)
		;
		$data = df_conn()->fetchRow($select);
		if ($data) {
			$reward->addData($data);
		}
		$this->_afterLoad($reward);
		return $this;
	}

	/**
	 * Prepare orphan points by given website id and website base currency code
	 * after website was deleted
	 * @param integer $websiteId
	 * @param string $baseCurrencyCode
	 * @return Df_Reward_Model_Resource_Reward
	 */
	public function prepareOrphanPoints($websiteId, $baseCurrencyCode) {
		if ($websiteId) {
			$this->_getWriteAdapter()->update(
				$this->getMainTable()
				,array('website_id' => null, 'website_currency_code' => $baseCurrencyCode)
				, df_db_quote_into('website_id = ?', $websiteId)
			);
		}
		return $this;
	}

	/**
	 * Save salesrule reward points delta
	 * @param integer $ruleId
	 * @param integer $pointsDelta
	 * @return Df_Reward_Model_Resource_Reward
	 */
	public function saveRewardSalesrule($ruleId, $pointsDelta) {
		$select = df_select()
			->from(df_table('df_reward/reward_salesrule'), 'rule_id')
			->where('rule_id = ?', $ruleId);
		if (df_conn()->fetchOne($select)) {
			df_conn()->update(
				df_table('df_reward/reward_salesrule')
				, array('points_delta' => $pointsDelta)
				, df_db_quote_into('rule_id = ?', $ruleId)
			);
		}
		else {
			df_conn()->insert(df_table('df_reward/reward_salesrule'), array(
				'rule_id' => $ruleId,'points_delta' => $pointsDelta
			));
		}
		return $this;
	}

	/**
	 * Perform Row-level data update
	 * @param Df_Reward_Model_Reward $object
	 * @param array $data New data
	 * @return Df_Reward_Model_Resource_Reward
	 */
	public function updateRewardRow(Df_Reward_Model_Reward $object, $data) {
		if (!$object->getId() || !is_array($data)) {
			return $this;
		}
		$where = array($this->getIdFieldName().'=?' => $object->getId());
		$this->_getWriteAdapter()
			->update($this->getMainTable(), $data, $where);
		return $this;
	}

	/**
	 * Нельзя вызывать @see parent::_construct(),
	 * потому что это метод в родительском классе — абстрактный.
	 * @see Mage_Core_Model_Resource_Abstract::_construct()
	 * @override
	 * @return void
	 */
	protected function _construct() {$this->_init(self::TABLE, Df_Reward_Model_Reward::P__ID);}
	/**
	 * @used-by Df_Reward_Model_Resource_Reward_History::isExistHistoryUpdate()
	 * @used-by Df_Reward_Model_Resource_Reward_History::getTotalQtyRewards()
	 * @used-by Df_Reward_Model_Resource_Reward_History::expirePoints()
	 * @used-by Df_Reward_Model_Resource_Reward_History_Collection::_joinReward()
	 * @used-by Df_Reward_Setup_1_0_0::_process()
	 * @used-by Df_Reward_Setup_1_0_1::_process()
	 */
	const TABLE = 'df_reward/reward';
	/** @return Df_Reward_Model_Resource_Reward */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}