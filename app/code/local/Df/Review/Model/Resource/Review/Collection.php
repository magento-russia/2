<?php
class Df_Review_Model_Resource_Review_Collection extends Mage_Review_Model_Mysql4_Review_Collection {
	/**
	 * В Magento CE 1.4 класс @see Mage_Review_Model_Mysql4_Review_Collection
	 * унаследован напрямую от класса @see Varien_Data_Collection_Db
	 * и не содержит методов @see Mage_Review_Model_Mysql4_Review_Collection::_construct()
	 * и @see Mage_Review_Model_Mysql4_Review_Collection::_init().
	 * Поэтому реализуем логику отсутствующих методов своим способом.
	 * своим способом.
	 * @override
	 * @return Df_Review_Model_Resource_Review_Collection
	 */
	public function __construct() {
		parent::__construct();
		if (self::isOldInterface()) {
			$this->setItemObjectClass(Df_Review_Model_Review::_CLASS);
		}
	}

	/** @return Df_Review_Model_Resource_Review_Collection */
	public function limitLast() {
		$this->setDateOrder('DESC');
		$this->getSelect()->limit(1);
		return $this;
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		if (!self::isOldInterface()) {
			parent::_construct();
			$this->_init(Df_Review_Model_Review::mf(), Df_Review_Model_Resource_Review::mf());
		}
	}

	const _CLASS = __CLASS__;
	/** @return Df_Review_Model_Resource_Review_Collection */
	public static function i() {return new self;}

	/**
	 * В Magento CE 1.4 класс @see Mage_Review_Model_Mysql4_Review_Collection
	 * унаследован напрямую от класса @see Varien_Data_Collection_Db
	 * и не содержит методов @see Mage_Review_Model_Mysql4_Review_Collection::_construct()
	 * и @see Mage_Review_Model_Mysql4_Review_Collection::_init().
	 * Поэтому реализуем логику отсутствующих методов своим способом.
	 * своим способом.
	 * @return bool
	 */
	private static function isOldInterface() {
		/** @var bool $result */
		static $result;
		if (!isset($result)) {
			/**
			 * Раньше тут стояло:
			 * $result = !method_exists('self', '_init') || !method_exists('self', '_construct');
			 * В моей версии PHP 5.5.12 вызов @see method_exists с первым параметром 'self' работает:
			 * method_exists('self', '_init').
			 * Однако 2014-10-14 заметил, что у клиентов, использующих PHP 5.3 (5.3.3 и 5..3.23)
			 * такой вызов не работает:
			 * @link https://mail.google.com/mail/u/0/#search/Warning%3A+include(Self.php)
			 * @link https://bugs.php.net/bug.php?id=50289
			 */
			$result = !method_exists(__CLASS__, '_init') || !method_exists(__CLASS__, '_construct');
		}
		return $result;
	}
}