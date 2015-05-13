<?php
/**
 * Наследуемся от Mage_Core_Model_Mysql4_Abstract для совместимости с Magento CE 1.4
 */
class Df_Core_Model_Resource_Location extends Mage_Core_Model_Mysql4_Abstract {
	/**
	 * @param Mage_Core_Model_Resource_Setup $setup
	 * @return Df_Warehousing_Model_Resource_Warehouse
	 */
	public function tableCreate(Mage_Core_Model_Resource_Setup $setup) {
		$f_CITY = Df_Core_Model_Location::P__CITY;
		$f_COUNTRY_ISO2 = Df_Core_Model_Location::P__COUNTRY_ISO2;
		$f_DIRECTORY_COUNTRY_REGION__REGION_ID = Df_Directory_Model_Region::P__REGION_ID;
		$f_LATITUDE = Df_Core_Model_Location::P__LATITUDE;
		$f_LONGITUDE = Df_Core_Model_Location::P__LONGITUDE;
		$f_LOCATION_ID = Df_Core_Model_Location::P__ID;
		$f_PHONE = Df_Core_Model_Location::P__PHONE;
		$f_POSTAL_CODE = Df_Core_Model_Location::P__POSTAL_CODE;
		$f_REGION_ID = Df_Core_Model_Location::P__REGION_ID;
		$f_REGION_NAME = Df_Core_Model_Location::P__REGION_NAME;
		$f_STREET_ADDRESS = Df_Core_Model_Location::P__STREET_ADDRESS;
		$t_DF_CORE_LOCATION = rm_table(self::TABLE_NAME);
		$t_DIRECTORY_COUNTRY_REGION = rm_table(Df_Directory_Model_Resource_Region::TABLE__PRIMARY);
		/**
		 * Не используем $this->getConnection()->newTable()
		 * для совместимости с Magento CE 1.4
		 */
		$setup->run("
			create table if not exists `{$t_DF_CORE_LOCATION}` (
				`{$f_LOCATION_ID}` int(10) unsigned not null auto_increment
				,`{$f_CITY}` varchar(100) not null
				,`{$f_COUNTRY_ISO2}` varchar(2) not null
				,`{$f_LATITUDE}` float(10,6) null default null
				,`{$f_LONGITUDE}` float(10,6) null default null
				,`{$f_PHONE}` varchar(20) not null
				,`{$f_POSTAL_CODE}` varchar(6) not null
				,`{$f_REGION_ID}` int(10) unsigned null default null
				,`{$f_STREET_ADDRESS}` varchar(255) not null
				,`{$f_REGION_NAME}` varchar(100) null default null
				,constraint `FK_DF_CORE_LOCATION__REGION_ID`
					foreign key (`{$f_REGION_ID}`)
					references `{$t_DIRECTORY_COUNTRY_REGION}`
						(`{$f_DIRECTORY_COUNTRY_REGION__REGION_ID}`)
					on delete cascade
					on update cascade
				,primary key  (`{$f_LOCATION_ID}`)
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
		$this->_init(self::TABLE_NAME, Df_Core_Model_Location::P__ID);
	}
	const _CLASS = __CLASS__;
	const TABLE_NAME = 'df_core/location';
	/**
	 * @see Df_Core_Model_Location::_construct()
	 * @see Df_Core_Model_Resource_Location_Collection::_construct()
	 * @return string
	 */
	public static function mf() {static $r; return $r ? $r : $r = rm_class_mf_r(__CLASS__);}
	/** @return Df_Core_Model_Resource_Location */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}