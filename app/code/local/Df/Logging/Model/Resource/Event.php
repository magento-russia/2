<?php
class Df_Logging_Model_Resource_Event extends Df_Core_Model_Resource {
	/**
	 * Select all values of specified field from main table
	 * @param string $field
	 * @param bool $order
	 * @return array
	 */
	public function getAllFieldValues($field, $order = true) {
		$field = df_db_quote($field);
		return
			$this->_getReadAdapter()->fetchCol(
				"SELECT DISTINCT {$field}
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
		return df_fetch_col_int(Df_Logging_Model_Resource_Event_Changes::TABLE, 'id', 'event_id', $eventId);
	}

	/**
	 * Get all admin usernames that are currently in event log table
	 * Possible SQL-performance issue
	 * @return string[]
	 */
	public function getUserNames() {
		return $this->_getReadAdapter()->fetchCol(
			$this->_getReadAdapter()->select()
				->distinct()
				->from(array('admins' => df_table('admin/user')), 'username')
				->joinInner(
					array('events' => df_table(self::TABLE))
					,'admins.username = events.user'
					,null
				))
		;
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
			$table = df_table(self::TABLE);
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
	 * @param Df_Logging_Model_Event|Mage_Core_Model_Abstract $event
	 * @return Df_Logging_Model_Resource_Event
	 */
	protected function _beforeSave(Mage_Core_Model_Abstract $event) {
		$event->setData('ip', ip2long($event->getIp()));
		$event->setTime($this->formatDate($event->getTime()));
		parent::_beforeSave($event);
		return $this;
	}

	/**
	 * Нельзя вызывать @see parent::_construct(),
	 * потому что это метод в родительском классе — абстрактный.
	 * @see Mage_Core_Model_Mysql4_Abstract::_construct()
	 * @override
	 * @return void
	 */
	protected function _construct() {$this->_init(self::TABLE, Df_Logging_Model_Event::P__ID);}
	/** @used-by Df_Logging_Setup_1_0_0::_process() */
	const TABLE = 'df_logging/event';
	/** @return Df_Logging_Model_Resource_Event */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}