<?php
class Df_Reward_Model_Resource_Reward extends Mage_Core_Model_Mysql4_Abstract {
	/**
	 * Delete orphan (points of deleted website) points by given customer
	 * @param integer $customerId
	 * @return Df_Reward_Model_Resource_Reward
	 */
	public function deleteOrphanPointsByCustomer($customerId) {
		if ($customerId) {
			$this->_getWriteAdapter()->delete(
				$this->getMainTable()
				,rm_quote_into('customer_id = ?', $customerId) . ' AND `website_id` IS null'
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
		$select = $this->_getReadAdapter()->select()->from(rm_table('df_reward/reward_salesrule'), $cols = '*');
		if (is_array($rule)) {
			$select->where('rule_id IN (?)', $rule);
			$data = $this->_getReadAdapter()->fetchAll($select);
		} else if (intval($rule)) {
			$select->where('rule_id = ?', intval($rule));
			$data = $this->_getReadAdapter()->fetchRow($select);
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
		$select = $this->_getReadAdapter()->select()
			->from($this->getMainTable(), $cols = '*')
			->where('customer_id = ?', $customerId)
			->where('website_id = ?', $websiteId)
		;
		$data = $this->_getReadAdapter()->fetchRow($select);
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
				, rm_quote_into('website_id = ?', $websiteId)
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
		$select = $this->_getWriteAdapter()->select()
			->from(rm_table('df_reward/reward_salesrule'), array('rule_id'))
			->where('rule_id = ?', $ruleId);
		if ($this->_getWriteAdapter()->fetchOne($select)) {
			$this->_getWriteAdapter()->update(
				rm_table('df_reward/reward_salesrule')
				, array('points_delta' => $pointsDelta)
				, rm_quote_into('rule_id = ?', $ruleId)
			);
		} else {
			$this->_getWriteAdapter()->insert(rm_table('df_reward/reward_salesrule'), array(
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
	 * @override
	 * @return void
	 */
	protected function _construct() {
		/**
		 * Нельзя вызывать parent::_construct(),
		 * потому что это метод в родительском классе — абстрактный.
		 * @see Mage_Core_Model_Resource_Abstract::_construct()
		 */
		$this->_init(self::TABLE_NAME, Df_Reward_Model_Reward::P__ID);
	}
	const _CLASS = __CLASS__;
	const TABLE_NAME = 'df_reward/reward';
	/**
	 * @see Df_Reward_Model_Reward::_construct()
	 * @see Df_Reward_Model_Resource_Reward_Collection::_construct()
	 * @return string
	 */
	public static function mf() {static $r; return $r ? $r : $r = rm_class_mf_r(__CLASS__);}
	/** @return Df_Reward_Model_Resource_Reward */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}