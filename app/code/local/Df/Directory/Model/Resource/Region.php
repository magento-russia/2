<?php
/**
 * В Magento CE < 1.6
 * класс Mage_Directory_Model_Mysql4_Region не наследован от Mage_Core_Model_Mysql4_Abstract,
 * что приводит к сбоям моего установочного скрипта при вызове метода save().
 *
 * Поэтому для Magento CE < 1.6 перекрываем класс Mage_Directory_Model_Mysql4_Region
 * классом Df_Directory_Model_Resource_Region, программный код которого взят из
 * класса Mage_Directory_Model_Resource_Region для Magento CE >= 1.6
 */
class Df_Directory_Model_Resource_Region extends Df_Core_Model_Resource {
	/**
	 * @param Mage_Directory_Model_Region $region
	 * @param string $regionCode
	 * @param string $countryId
	 * @return Mage_Directory_Model_Resource_Region
	 */
	public function loadByCode(Mage_Directory_Model_Region $region, $regionCode, $countryId) {
		return $this->_loadByCountry($region, $countryId, (string)$regionCode, 'code');
	}

	/**
	 * Load data by country id and default region name
	 *
	 * @param Mage_Directory_Model_Region $region
	 * @param string $regionName
	 * @param string $countryId
	 * @return Mage_Directory_Model_Resource_Region
	 */
	public function loadByName(Mage_Directory_Model_Region $region, $regionName, $countryId) {
		return $this->_loadByCountry(
			$region, $countryId, (string)$regionName, Df_Directory_Model_Region::P__DEFAULT_NAME
		);
	}

	/**
	 * Retrieve select object for load object data
	 *
	 * @param string $field
	 * @param mixed $value
	 * @param Mage_Core_Model_Abstract $object
	 * @return Varien_Db_Select
	 */
	protected function _getLoadSelect($field, $value, $object) {
		$select  = parent::_getLoadSelect($field, $value, $object);
		$adapter = $this->_getReadAdapter();
		$locale	   = df_locale();
		$systemLocale = Mage::app()->getDistroLocaleCode();
		$regionField = df_db_quote($this->getMainTable() . '.' . $this->getIdFieldName());
		$condition = df_db_quote_into('lrn.locale = ?', $locale);
		$select->joinLeft(
			array('lrn' => df_table(self::TABLE__NAME))
			,"{$regionField} = lrn.region_id AND {$condition}"
			,array()
		);
		if ($locale != $systemLocale) {
			/**
			 * 2016-10-16
			 * Метод @see Varien_Db_Adapter_Pdo_Mysql::getCheckSql() отсутствует в Magento CE 1.4,
			 * однако Magento CE 1.4 больше не поддерживаем.
			 */
			$nameExpr = $adapter->getCheckSql('lrn.region_id is null', 'srn.name', 'lrn.name');
			$condition = df_db_quote_into('srn.locale = ?', $systemLocale);
			$select->joinLeft(
				array('srn' => df_table(self::TABLE__NAME))
				,"{$regionField} = srn.region_id AND {$condition}"
				,array('name' => $nameExpr)
			);
		}
		else {
			$select->columns(array('name'), 'lrn');
		}
		return $select;
	}

	/**
	 * Load object by country id and code or default name
	 *
	 * @param Mage_Core_Model_Abstract $object
	 * @param int $countryId
	 * @param string $value
	 * @param string $field
	 * @return Mage_Directory_Model_Resource_Region
	 */
	protected function _loadByCountry($object, $countryId, $value, $field)
	{
		$adapter		= $this->_getReadAdapter();
		$locale		 = df_locale();
		$joinCondition  = df_db_quote_into('rname.region_id = region.region_id AND rname.locale = ?', $locale);
		$select		 = $adapter->select()
			->from(array('region' => $this->getMainTable()))
			->joinLeft(
				array('rname' => df_table(self::TABLE__NAME)),$joinCondition,array('name'))
			->where('region.country_id = ?', $countryId)
			->where("region.{$field} = ?", $value);
		$data = $adapter->fetchRow($select);
		if ($data) {
			$object->setData($data);
		}
		$this->_afterLoad($object);
		return $this;
	}

	/**
	 * Нельзя вызывать @see parent::_construct(),
	 * потому что это метод в родительском классе — абстрактный.
	 * @see Mage_Core_Model_Mysql4_Abstract::_construct()
	 * @override
	 * @return void
	 */
	protected function _construct() {
		$this->_init(self::TABLE, Df_Directory_Model_Region::P__REGION_ID);
	}
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
	/** @return Df_Directory_Model_Resource_Region */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}