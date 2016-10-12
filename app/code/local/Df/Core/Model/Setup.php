<?php
abstract class Df_Core_Model_Setup extends Df_Core_Model {
	/**
	 * @abstract
	 * @return void
	 */
	abstract public function process();

	/**
	 * Обратите внимание, что метод @see Varien_Db_Adapter_Pdo_Mysql::lastInsertId()
	 * почему-то отсутствует в интерфейсе @see Varien_Db_Adapter_Interface (видимо, по недосмотру),
	 * однако используется установочными скриптами.
	 * @return Varien_Db_Adapter_Interface|Varien_Db_Adapter_Pdo_Mysql
	 */
	protected function conn() {return $this->getSetup()->getConnection();}

	/** @return Df_Core_Model_Resource_Setup */
	protected function getSetup() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->cfg(self::P__SETUP);
			if (!$this->{__METHOD__}) {
				$this->{__METHOD__} = Df_Core_Model_Resource_Setup::s();
			}
			df_assert($this->{__METHOD__} instanceof Df_Core_Model_Resource_Setup);
		}
		return $this->{__METHOD__};
	}

	/** @return Mage_Sales_Model_Mysql4_Setup|Mage_Sales_Model_Resource_Setup */
	protected function getSetupSales() {
		if (!isset($this->{__METHOD__})) {
			/** @var Mage_Sales_Model_Mysql4_Setup|Mage_Sales_Model_Resource_Setup $result */
			/**
			 * Обратите внимание, что мы не можем использовать @see Mage::getResourceSingleton(),
			 * потому что этот метод требует, чтобы второй параметр его конструктора был массивом.
			 * По этой причине мы вынуждены использовать @see Mage::getResourceModel()
			 * и кэшировать результат вручную.
			 */
			$result = Mage::getResourceModel('sales/setup', 'sales_setup');
			/**
			 * Обратите внимание, что в новых версиях Magento CE
			 * Mage::getResourceModel('sales/setup')
			 * вернёт объект класса @see Mage_Sales_Model_Resource_Setup,
			 * а в старых версиях — объект класса @see Mage_Sales_Model_Mysql4_Setup.
			 */
			df_assert(
						@class_exists('Mage_Sales_Model_Resource_Setup')
					&&
						$result instanceof Mage_Sales_Model_Resource_Setup
				||
					$result instanceof Mage_Sales_Model_Mysql4_Setup
			);
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param string $sql
	 * @return void
	 */
	protected function runSilent($sql) {
		try {
			$this->getSetup()->run($sql);
		}
		catch(Exception $e) {}
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__SETUP, Df_Core_Model_Resource_Setup::_CLASS, false);
	}
	const _CLASS = __CLASS__;
	const P__SETUP = 'setup';

	/** @return Df_Catalog_Model_Resource_Installer_Attribute */
	protected static function attribute() {return Df_Catalog_Model_Resource_Installer_Attribute::s();}

	/**
	 * @param Df_Core_Model_Resource_Setup $setup
	 * @param string $class
	 * @return Df_Core_Model_Setup
	 */
	protected static function ic(Df_Core_Model_Resource_Setup $setup, $class) {
		/** @var Df_Core_Model_Setup $result */
		$result = new $class(array(self::P__SETUP => $setup));
		df_assert($result instanceof Df_Core_Model_Setup);
		return $result;
	}
}