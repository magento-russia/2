<?php
/**
 * Наследуемся от Mage_Core_Model_Mysql4_Abstract для совместимости с Magento CE 1.4
 */
class Df_Warehousing_Model_Resource_Warehouse extends Mage_Core_Model_Mysql4_Abstract {
	/**
	 * @param Mage_Core_Model_Resource_Setup $setup
	 * @return Df_Warehousing_Model_Resource_Warehouse
	 */
	public function tableCreate(Mage_Core_Model_Resource_Setup $setup) {
		$f_DF_CORE_LOCATION__LOCATION_ID = Df_Core_Model_Location::P__ID;
		$f_LOCATION_ID = Df_Warehousing_Model_Warehouse::P__LOCATION_ID;
		$f_NAME = Df_Warehousing_Model_Warehouse::P__NAME;
		$f_WAREHOUSE_ID = Df_Warehousing_Model_Warehouse::P__ID;
		$t_DF_CORE_LOCATION = rm_table(Df_Core_Model_Resource_Location::TABLE_NAME);
		$t_DF_WAREHOUSING_WAREHOUSE = rm_table(self::TABLE_NAME);
		/**
		 * Не используем $this->getConnection()->newTable()
		 * для совместимости с Magento CE 1.4
		 */
		$setup->run("
			create table if not exists `{$t_DF_WAREHOUSING_WAREHOUSE}` (
				`{$f_WAREHOUSE_ID}` int(10) unsigned not null auto_increment
				,`{$f_NAME}` varchar(100) not null
				,`{$f_LOCATION_ID}` int(10) unsigned null default null
				,CONSTRAINT `FK_DF_WAREHOUSING_WAREHOUSE__LOCATION_ID`
					foreign key (`{$f_LOCATION_ID}`)
					references `{$t_DF_CORE_LOCATION}`
						(`{$f_DF_CORE_LOCATION__LOCATION_ID}`)
						on delete cascade
						on update cascade
				,primary key (`{$f_WAREHOUSE_ID}`)
			) engine=InnoDB default charset=utf8;
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
		$this->_init(self::TABLE_NAME, Df_Warehousing_Model_Warehouse::P__ID);
	}
	const _CLASS = __CLASS__;
	const TABLE_NAME = 'df_warehousing/warehouse';
	/**
	 * @see Df_Warehousing_Model_Warehouse::_construct()
	 * @return string
	 */
	public static function mf() {static $r; return $r ? $r : $r = rm_class_mf_r(__CLASS__);}
	/** @return Df_Warehousing_Model_Resource_Warehouse */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}