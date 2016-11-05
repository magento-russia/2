<?php
/**
 * В Magento CE < 1.6
 * класс Mage_Directory_Model_Mysql4_Region не наследован от Mage_Core_Model_Mysql4_Abstract,
 * что приводит к сбоям моего установочного скрипта при вызове метода save().
 *
 * Поэтому для Magento CE < 1.6 перекрываем класс Mage_Directory_Model_Mysql4_Region
 * классом Df_Directory_Model_Resource_Region, программный код которого взят из
 * класса Mage_Directory_Model_Resource_Region для Magento CE >= 1.6
 *
 * 2016-10-16
 * Упомянутые выше устаревшие версии Magento CE мы отныне не поддерживаем.
 */
class Df_Directory_Model_Resource_Region extends Mage_Directory_Model_Resource_Region {
	/**
	 * @used-by Df_Directory_Setup_2_0_0::_process()
	 * @used-by Df_Directory_Setup_Processor_InstallRegions::regionsDelete()
	 * @used-by Df_Directory_Setup_Processor_InstallRegions::regionInsert()
	 */
	const TABLE = 'directory/country_region';
	/**
	 * @used-by Df_Directory_Model_Resource_Region_Collection::_construct()
	 * @used-by Df_Directory_Setup_Processor_InstallRegions::regionInsert()
	 * @used-by Df_Directory_Setup_Processor_Region::getTableRegionName()
	 */
	const TABLE__NAME = 'directory/country_region_name';
	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}