<?php
class Df_Reward_Model_Resource_Reward_History extends Df_Core_Model_Resource {
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
		$select = rm_select()
			->from(array('reward_table' => rm_table(Df_Reward_Model_Resource_Reward::TABLE)), null)
			->joinInner(array('history_table' => $this->getMainTable()),'history_table.reward_id = reward_table.reward_id', null)
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
				array('reward_table' => rm_table(Df_Reward_Model_Resource_Reward::TABLE))
				,'history_table.reward_id = reward_table.reward_id'
				, null
			)
			->where("history_table.action=?", $action)
			->where("reward_table.customer_id=?", $customerId)
			->where("history_table.website_id=?", $websiteId);
		return (int)$this->_getReadAdapter()->fetchOne($select);
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
			/** @noinspection PhpAssignmentInConditionInspection */
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
			if ($updateSqlValues) {
				$updateSql =
					$updateSql
				   . df_csv($updateSqlValues)
				   . " ON DUPLICATE KEY UPDATE `points_used`=VALUES(`points_used`) "
				;
				$this->_getWriteAdapter()->query($updateSql);
			}
			$this->_getWriteAdapter()->commit();
		} catch (Exception $e) {
			$this->_getWriteAdapter()->rollback();
			df_error($e);
		}
		return $this;
	}

	/**
	 * @param int $days
	 * @param int|int[] $websiteIds
	 * @return Df_Reward_Model_Resource_Reward_History
	 */
	public function updateExpirationDate($days, $websiteIds) {
		$days = (int)abs($days);
		if ($days) {
			$newValue = "ADDDATE(`created_at`, INTERVAL {$days} DAY)";
		} else {
			$newValue = '0000-00-00 00:00:00';
		}
		$sql = "UPDATE `{$this->getMainTable()}` SET `expired_at_dynamic`={$newValue} WHERE ";
		$sql.= rm_quote_into("`website_id` in (?)", rm_array($websiteIds));
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

		if ($expiredHistoryIds) {
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
					rm_table(Df_Reward_Model_Resource_Reward::TABLE)
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
	 * Нельзя вызывать @see parent::_construct(),
	 * потому что это метод в родительском классе — абстрактный.
	 * @see Mage_Core_Model_Mysql4_Abstract::_construct()
	 * @override
	 * @return void
	 */
	protected function _construct() {$this->_init(self::TABLE, Df_Reward_Model_Reward_History::P__ID);}
	/**
	 * @used-by Df_Reward_Setup_1_0_0::_process()
	 * @used-by Df_Reward_Setup_1_0_1::_process()
	 * @used-by Df_Reward_Setup_2_20_6::_process()
	 */
	const TABLE = 'df_reward/reward_history';
	/** @return Df_Reward_Model_Resource_Reward_History */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}