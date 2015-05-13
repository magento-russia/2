<?php
/**
 * Наследуемся от Mage_Core_Model_Mysql4_Abstract для совместимости с Magento CE 1.4
 */
class Df_Client_Model_Resource_DelayedMessage extends Mage_Core_Model_Mysql4_Abstract {
	/**
	 * @param Mage_Core_Model_Resource_Setup $setup
	 * @return Df_Client_Model_Resource_DelayedMessage
	 */
	public function createTable(Mage_Core_Model_Resource_Setup $setup) {
		/** @var string $tableName */
		$tableName = rm_table(self::TABLE_NAME);
		/** @var string $fieldBody */
		$fieldBody = Df_Client_Model_DelayedMessage::P__BODY;
		/** @var string $fieldClassName */
		$fieldClassName = Df_Client_Model_DelayedMessage::P__CLASS_NAME;
		/** @var string $fieldCreationTime */
		$fieldCreationTime = Df_Client_Model_DelayedMessage::P__CREATION_TIME;
		/** @var string $fieldLastRetryTime */
		$fieldLastRetryTime = Df_Client_Model_DelayedMessage::P__LAST_RETRY_TIME;
		/** @var string $fieldMessageId */
		$fieldMessageId = Df_Client_Model_DelayedMessage::P__MESSAGE_ID;
		/** @var string $fieldNumRetries*/
		$fieldNumRetries = Df_Client_Model_DelayedMessage::P__NUM_RETRIES;
		/**
		 * Не используем $this->getConnection()->newTable()
		 * для совместимости с Magento CE 1.4
		 */
		$setup->run("
			CREATE TABLE IF NOT EXISTS `{$tableName}` (
				`{$fieldMessageId}` int(10) unsigned NOT null auto_increment
				,`{$fieldBody}` VARBINARY(1000) NOT null
				,`{$fieldClassName}` VARBINARY(100) NOT null
				,`{$fieldCreationTime}` TIMESTAMP null DEFAULT null
				,`{$fieldLastRetryTime}` TIMESTAMP null DEFAULT null
				,`{$fieldNumRetries}` int(4) unsigned NOT null DEFAULT 0
				,PRIMARY KEY  (`{$fieldMessageId}`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		");
		/**
		 * После изменения структуры базы данных надо удалить кэш,
		 * потому что Magento кэширует структуру базы данных
		 */
		rm_cache_clean();
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
		$this->_init(self::TABLE_NAME, Df_Client_Model_DelayedMessage::P__MESSAGE_ID);
	}
	const _CLASS = __CLASS__;
	const TABLE_NAME = 'df_client/message';
	/**
	 * @see Df_Client_Model_DelayedMessage::_construct()
	 * @see Df_Client_Model_Resource_DelayedMessage_Collection::_construct()
	 * @return string
	 */
	public static function mf() {static $r; return $r ? $r : $r = rm_class_mf_r(__CLASS__);}
	/** @return Df_Client_Model_Resource_DelayedMessage */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}