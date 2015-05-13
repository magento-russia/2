<?php
class Df_AdminNotification_Model_Resource_Inbox extends Mage_AdminNotification_Model_Mysql4_Inbox {
	/**
	 * Цель перекрытия —
	 * скрывать «Reminder: Change Magento`s default phone numbers»
	 * после пометки прочитанным.
	 * @override
	 * @param Mage_AdminNotification_Model_Inbox $object
	 * @param array(array(string => string)) $data
	 * @return void
	 */
	public function parse(Mage_AdminNotification_Model_Inbox $object, array $data) {
		/**
		 * Для совместимости с модулем M-Turbo, который вызывает метод parse
		 * прямо из установочного скрипта
		 */
		if (!function_exists('df_enabled')) {
			parent::parse($object, $data);
		}
		else {
			/** @var bool $patchNeeded */
			static $patchNeeded;
			if (!isset($patchNeeded)) {
				$patchNeeded =
						df_enabled(Df_Core_Feature::TWEAKS_ADMIN)
					&&
						df_cfg()->admin()->system()->notifications()->getFixReminder()
				;
			}
			if ($patchNeeded) {
				$this->parseDf($object, $data);
			}
			else {
				parent::parse($object, $data);
			}
		}
	}

	/**
	 * @param Mage_AdminNotification_Model_Inbox $object
	 * @param array(array(string => string)) $data
	 */
	public function parseDf(Mage_AdminNotification_Model_Inbox $object, array $data) {
		/** @var Varien_Db_Adapter_Pdo_Mysql $adapter */
		$adapter = $this->_getWriteAdapter();
		/**
		 * В Magento ранее версии 1.6 отсутствует интерфейс Varien_Db_Adapter_Interface,
		 * поэтому там адаптер принадлежит к классу Varien_Db_Adapter_Pdo_Mysql
		 */
		foreach ($data as $item) {
			/** @var array(string => string) $item */
			df_assert_array($item);
			/** @var Varien_Db_Select $select */
			$select =
				$adapter->select()
					->from($this->getMainTable())
					->where('url=? OR url IS null', $item['url'])
					->where('title=?', $item['title'])
			;
			/** @var array|bool|null $row */
			$row = false;
			if (isset($item['internal'])) {
				$row = false;
				unset($item['internal']);
			} else {
				$row = $adapter->fetchRow($select);
			}
			if (!$row) {
				$adapter->insert($this->getMainTable(), $item);
			}
		}
	}
}