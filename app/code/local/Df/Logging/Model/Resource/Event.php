<?php
class Df_Logging_Model_Resource_Event extends Mage_Core_Model_Mysql4_Abstract {
	/**
	 * Select all values of specified field from main table
	 * @param string $field
	 * @param bool $order
	 * @return array
	 */
	public function getAllFieldValues($field, $order = true) {
		return
			$this->_getReadAdapter()->fetchCol(
				"SELECT DISTINCT {$this->_getReadAdapter()->quoteIdentifier($field)}
				FROM {$this->getMainTable()}"
				. (null !== $order ? ' ORDER BY 1' . ($order ? '' : ' DESC') : '')
			)
		;
	}

	/**
	 * Get event change ids of specified event
	 * @param int $eventId
	 * @return int[]
	 */
	public function getEventChangeIds($eventId) {
		$adapter = $this->_getReadAdapter();
		$select =
			$adapter->select()
				->from(
					rm_table(Df_Logging_Model_Resource_Event_Changes::TABLE_NAME)
					,array('id')
				)
				->where('event_id = ?', $eventId)
		;
		/** @var int[] $result */
		$result = $adapter->fetchCol($select);
		return $result;
	}

	/**
	 * Get all admin usernames that are currently in event log table
	 * Possible SQL-performance issue
	 * @return string[]
	 */
	public function getUserNames() {
		$select =
			$this->_getReadAdapter()->select()
				->distinct()
				->from(
					array('admins' => rm_table('admin/user'))
					,'username'
				)
				->joinInner(
					array('events' => rm_table('df_logging/event'))
					,'admins.username = events.user'
					,array()
				)
		;
		/** @var string[] $result */
		$result = $this->_getReadAdapter()->fetchCol($select);
		return $result;
	}

	/**
	 * Rotate logs - get from database and pump to CSV-file
	 * @param int $lifetime
	 */
	public function rotate($lifetime) {
		try {
			$this->beginTransaction();
			// make sure folder for dump file will exist
			/** @var Df_Logging_Model_Archive $archive */
			/** @var Df_Logging_Model_Archive $archive */
			$archive = Df_Logging_Model_Archive::i();
			$archive->createNew();
			$table = rm_table('df_logging/event');
			// get the latest log entry required to the moment
			$clearBefore = $this->formatDate(time() - $lifetime);
			$latestLogEntry = $this->_getWriteAdapter()->fetchOne("SELECT log_id FROM {$table}
				WHERE `time` < '{$clearBefore}' ORDER BY 1 DESC LIMIT 1");
			if (!$latestLogEntry) {
				return;
			}

			// dump all records before this log entry into a CSV-file
			$csv = fopen($archive->getFilename(), 'w');
			foreach ($this->_getWriteAdapter()->fetchAll("SELECT *, INET_NTOA(ip)
				FROM {$table} WHERE log_id <= {$latestLogEntry}") as $row) {
				fputcsv($csv, $row);
			}
			fclose($csv);
			$this->_getWriteAdapter()->query("DELETE FROM {$table} WHERE log_id <= {$latestLogEntry}");
			$this->commit();
		} catch (Exception $e) {
			$this->rollBack();
		}
	}

	/**
	 * @override
	 * @param Df_Logging_Model_Resource_Event|Mage_Core_Model_Abstract $event
	 * @return Df_Logging_Model_Resource_Event
	 */
	protected function _beforeSave(Mage_Core_Model_Abstract $event) {
		$event->setData('ip', ip2long($event->getIp()));
		$event->setTime($this->formatDate($event->getTime()));
		parent::_beforeSave($event);
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
		$this->_init(self::TABLE_NAME, Df_Logging_Model_Event::P__ID);
	}
	const _CLASS = __CLASS__;
	const TABLE_NAME = 'df_logging/event';
	/**
	 * @see Df_Logging_Model_Event::_construct()
	 * @see Df_Logging_Model_Resource_Event_Collection::_construct()
	 * @return string
	 */
	public static function mf() {static $r; return $r ? $r : $r = rm_class_mf_r(__CLASS__);}
	/** @return Df_Logging_Model_Resource_Event */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}