<?php
class Df_Reward_Model_Resource_Reward_History extends Mage_Core_Model_Mysql4_Abstract {
	/**
	 * @override
	 * @param Mage_Core_Model_Abstract $object
	 * @return Df_Reward_Model_Resource_Reward_History
	 */
	protected function _afterLoad(Mage_Core_Model_Abstract $object) {
		parent::_afterLoad($object);
		if (is_string($object->getData('additional_data'))) {
			$object->setData('additional_data', unserialize($object->getData('additional_data')));
		}
		return $this;
	}

	/**
	 * Perform actions before object save
	 *
	 * @param Mage_Core_Model_Abstract $object
	 * @return Df_Reward_Model_Resource_Reward_History
	 */
	public function _beforeSave(Mage_Core_Model_Abstract $object)
	{
		parent::_beforeSave($object);
		if (is_array($object->getData('additional_data'))) {
			$object->setData('additional_data', serialize($object->getData('additional_data')));
		}
		return $this;
	}

	/**
	 * Check if history update with given action, customer and entity exist
	 *
	 * @param integer $customerId
	 * @param integer $action
	 * @param integer $websiteId
	 * @param mixed $entity
	 * @return boolean
	 */
	public function isExistHistoryUpdate($customerId, $action, $websiteId, $entity)
	{
		$select = $this->_getWriteAdapter()->select()
			->from(array('reward_table' => rm_table('df_reward/reward')), array())
			->joinInner(array('history_table' => $this->getMainTable()),'history_table.reward_id = reward_table.reward_id', array())
			->where('history_table.action = ?', $action)
			->where('history_table.website_id = ?', $websiteId)
			->where('history_table.entity = ?', $entity)
			->columns(array('history_table.history_id'));
		if ($this->_getWriteAdapter()->fetchRow($select)) {
			return true;
		}
		return false;
	}

	/**
	 * Return total quantity rewards for specified action and customer
	 *
	 * @param int $action
	 * @param int $customerId
	 * @param integer $websiteId
	 * @return int
	 */
	public function getTotalQtyRewards($action, $customerId, $websiteId)
	{
		$select = $this->_getReadAdapter()->select()->from(array('history_table' => $this->getMainTable()), array('COUNT(*)'))
			->joinInner(
				array('reward_table' => rm_table('df_reward/reward'))
				,'history_table.reward_id = reward_table.reward_id'
				, array()
			)
			->where("history_table.action=?", $action)
			->where("reward_table.customer_id=?", $customerId)
			->where("history_table.website_id=?", $websiteId);
		return intval($this->_getReadAdapter()->fetchOne($select));
	}

	/**
	 * Retrieve actual history records that have unused points, i.e. points_delta-points_used > 0
	 * Update points_used field for non-used points
	 *
	 * @param Df_Reward_Model_Reward_History $history
	 * @param int $required Points total that required
	 * @return Df_Reward_Model_Resource_Reward_History
	 */
	public function useAvailablePoints($history, $required)
	{
		$required = (int)abs($required);
		if (!$required) {
			return $this;
		}

		try {
			$this->_getWriteAdapter()->beginTransaction();
			$select = $this->_getReadAdapter()->select()
				->from(array('history' => $this->getMainTable()), array('history_id', 'points_delta', 'points_used'))
				->where('reward_id=?', $history->getRewardId())
				->where('website_id=?', $history->getWebsiteId())
				->where('is_expired=0')
				->where('`points_delta`-`points_used`>0')
				->order('history_id')
				->forUpdate(true);
			$stmt = $this->_getReadAdapter()->query($select);
			$updateSql = "INSERT INTO `{$this->getMainTable()}` (`history_id`, `points_used`) VALUES ";
			$updateSqlValues = array();
			while ($row = $stmt->fetch()) {
				if ($required <= 0) {
					break;
				}
				$rowAvailable = $row['points_delta'] - $row['points_used'];
				$pointsUsed = min($required, $rowAvailable);
				$required -= $pointsUsed;
				$newPointsUsed = $pointsUsed + $row['points_used'];
				$updateSqlValues[]= " ('{$row['history_id']}', '{$newPointsUsed}') ";
			}
			if (count($updateSqlValues) > 0) {
				$updateSql = $updateSql
						   . implode(',', $updateSqlValues)
						   . " ON DUPLICATE KEY UPDATE `points_used`=VALUES(`points_used`) ";
				$this->_getWriteAdapter()->query($updateSql);
			}
			$this->_getWriteAdapter()->commit();
		} catch (Exception $e) {
			$this->_getWriteAdapter()->rollback();
			throw $e;
		}
		return $this;
	}

	/**
	 * Update history expired_at_dynamic field for specified websites when config changed
	 *
	 * @param int $days Reward Points Expire in (days)
	 * @param array $websiteIds Array of website ids that must be updated
	 * @return Df_Reward_Model_Resource_Reward_History
	 */
	public function updateExpirationDate($days, $websiteIds)
	{
		$websiteIds = is_array($websiteIds) ? $websiteIds : array($websiteIds);
		$days = (int)abs($days);
		if ($days) {
			$newValue = "ADDDATE(`created_at`, INTERVAL {$days} DAY)";
		} else {
			$newValue = '0000-00-00 00:00:00';
		}
		$sql = "UPDATE `{$this->getMainTable()}` SET `expired_at_dynamic`={$newValue} WHERE ";
		$sql.= rm_quote_into("`website_id` in (?)", $websiteIds);
		$this->_getWriteAdapter()->query($sql);
	}

	/**
	 * Make points expired for specified website
	 *
	 * @param int $websiteId
	 * @param string $expiryType Expiry calculation (static or dynamic)
	 * @param int $limit Limitation for records expired selection
	 * @return Df_Reward_Model_Resource_Reward
	 */
	public function expirePoints($websiteId, $expiryType, $limit) {
		/** @var string $now */
		$now = $this->formatDate(time());
		/** @var string $field */
		$field =
			'static' === $expiryType
			? 'expired_at_static'
			: 'expired_at_dynamic'
		;
		$select =
			$this->_getReadAdapter()->select()
				->from($this->getMainTable())
				->where('website_id=?', $websiteId)
				->where("`{$field}` < ?", $now)
				->where("`{$field}` IS NOT NULL")
				->where('is_expired=0')
				->where('`points_delta`-`points_used`>0')
				->limit((int)$limit)
		;
		$duplicates = array();
		$expiredAmounts = array();
		$expiredHistoryIds = array();
		$stmt = $this->_getReadAdapter()->query($select);
		while (true) {
			$row = $stmt->fetch();
			if (!$row) {
				break;
			}
			$row['created_at'] = $now;
			$row['expired_at_static'] = null;
			$row['expired_at_dynamic'] = null;
			$row['is_expired'] = '1';
			$row['is_duplicate_of'] = $row['history_id'];
			$expiredHistoryIds[]= $row['history_id'];
			unset($row['history_id']);
			if (!isset($expiredAmounts[$row['reward_id']])) {
				$expiredAmounts[$row['reward_id']] = 0;
			}
			$expiredAmount = $row['points_delta'] - $row['points_used'];
			$row['points_delta'] = -$expiredAmount;
			$row['points_used'] = 0;
			$expiredAmounts[$row['reward_id']] += $expiredAmount;
			$duplicates[]= $row;
		}

		if (count($expiredHistoryIds) > 0) {
			// decrease points balance of rewards
			foreach ($expiredAmounts as $rewardId => $expired) {
				if ($expired == 0) {
					continue;
				}
				$bind =
					array(
						'points_balance' =>
							new Zend_Db_Expr(
								"IF(`points_balance`>{$expired}, `points_balance`-{$expired}, 0)"
							)
					)
				;
				$this->_getWriteAdapter()->update(
					rm_table('df_reward/reward')
					, $bind
					, array('? = reward_id' => $rewardId)
				);
			}
			// duplicate expired records
			$this->_getWriteAdapter()->insertMultiple($this->getMainTable(), $duplicates);
			// update is_expired field (using history ids instead where clause for better performance)
			$this->_getWriteAdapter()
				->update(
					$this->getMainTable()
					,array('is_expired' => '1')
					,array('history_id IN (?)' => $expiredHistoryIds)
				)
			;
		}
		return $this;
	}

	/**
	 * Perform Row-level data update
	 * @param Df_Reward_Model_Reward_History $object
	 * @param array $data New data
	 * @return Df_Reward_Model_Resource_Reward
	 */
	public function updateHistoryRow(Df_Reward_Model_Reward_History $object, $data)
	{
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
		$this->_init(self::TABLE_NAME, Df_Reward_Model_Reward_History::P__ID);
	}
	const _CLASS = __CLASS__;
	const TABLE_NAME = 'df_reward/reward_history';
	/**
	 * @see Df_Reward_Model_Reward_History::_construct()
	 * @see Df_Reward_Model_Resource_Reward_History_Collection::_construct()
	 * @return string
	 */
	public static function mf() {static $r; return $r ? $r : $r = rm_class_mf_r(__CLASS__);}
	/** @return Df_Reward_Model_Resource_Reward_History */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}