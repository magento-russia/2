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
		if (!function_exists('df_cfg')) {
			parent::parse($object, $data);
		}
		else {
			/** @var bool $patchNeeded */
			static $patchNeeded;
			if (is_null($patchNeeded)) {
				$patchNeeded = df_cfg()->admin()->system()->notifications()->getFixReminder();
			}
			$patchNeeded ? $this->parseDf($object, $data) : parent::parse($object, $data);
		}
	}

	/**
	 * @param Mage_AdminNotification_Model_Inbox $object
	 * @param array(array(string => string)) $data
	 */
	public function parseDf(Mage_AdminNotification_Model_Inbox $object, array $data) {
		/** @var Varien_Db_Adapter_Pdo_Mysql $adapter */
		$adapter = $this->_getWriteAdapter();
		foreach ($data as $item) {
			/** @var array(string => string) $item */
			df_assert_array($item);
			/** @var Varien_Db_Select $select */
			$select =
				$adapter->select()
					->from($this->getMainTable(), $this->getIdFieldName())
					->where('(? = url) OR (url IS NULL)', $item['url'])
					->where('? = title', $item['title'])
			;
			/** @var array|bool|null $row */
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

	/**
	 * 2015-02-09
	 * Возвращаем объект-одиночку именно таким способом,
	 * потому что наш класс перекрывает посредством <rewrite>
	 * системный класс @see Mage_AdminNotification_Model_Resource_Inbox,
	 * и мы хотим, чтобы вызов @see Mage::getResourceSingleton() ядром Magento
	 * возвращал тот же объект, что и наш метод @see s(),
	 * сохраняя тем самым объект одиночкой (это важно, например, для производительности:
	 * сохраняя объект одиночкой — мы сохраняем его кэш между всеми пользователями объекта).
	 * @return Df_AdminNotification_Model_Resource_Inbox
	 */
	public static function s() {return Mage::getResourceSingleton('adminnotification/inbox');}
}