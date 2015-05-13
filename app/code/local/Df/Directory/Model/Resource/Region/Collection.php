<?php
class Df_Directory_Model_Resource_Region_Collection
	extends Mage_Core_Model_Mysql4_Collection_Abstract {
	/**
	 * Filter by country code (ISO 3)
	 * @param string $countryCode
	 * @return Df_Directory_Model_Resource_Region_Collection
	 */
	public function addCountryCodeFilter($countryCode) {
		$this->getSelect()
			->joinLeft(
				array('country' => $this->_countryTable)
				,'main_table.country_id = country.country_id'
			)
			->where('country.iso3_code = ?', $countryCode);
		return $this;
	}

	/**
	 * Filter by country_id
	 * @param string|array $countryId
	 * @return Df_Directory_Model_Resource_Region_Collection
	 */
	public function addCountryFilter($countryId) {
		if (!empty($countryId)) {
			if (is_array($countryId)) {
				$this->addFieldToFilter('main_table.country_id', array('in' => $countryId));
			} else {
				$this->addFieldToFilter('main_table.country_id', $countryId);
			}
		}
		return $this;
	}

	/**
	 * Filter by Region code
	 *
	 * @param string|array $regionCode
	 * @return Df_Directory_Model_Resource_Region_Collection
	 */
	public function addRegionCodeFilter($regionCode) {
		if (!empty($regionCode)) {
			if (is_array($regionCode)) {
				$this->addFieldToFilter('main_table.code', array('in' => $regionCode));
			} else {
				$this->addFieldToFilter('main_table.code', $regionCode);
			}
		}
		return $this;
	}

	/**
	 * Filter by region name
	 * @param string|array $regionName
	 * @return Df_Directory_Model_Resource_Region_Collection
	 */
	public function addRegionNameFilter($regionName) {
		if (!empty($regionName)) {
			if (is_array($regionName)) {
				$this->addFieldToFilter('main_table.default_name', array('in' => $regionName));
			} else {
				$this->addFieldToFilter('main_table.default_name', $regionName);
			}
		}
		return $this;
	}

	/**
	 * @param string $name
	 * @return int
	 */
	public function getIdByName($name) {
		/** @var Df_Directory_Model_Region|null $region */
		$region = $this->getItemByName($name);
		return is_null($region) ? null : rm_nat0($region->getId());
	}

	/**
	 * @param string $name
	 * @return Df_Directory_Model_Region|null
	 */
	public function getItemByName($name) {
		return df_a($this->getMapFromNameToItem(), mb_strtoupper($name));
	}

	/**
	 * Convert collection items to select options array
	 * @return array
	 */
	public function toOptionArray() {
		$options = $this->_toOptionArray('region_id', 'default_name', array('title' => 'default_name'));
		if (count($options) > 0) {
			array_unshift(
				$options
				,array(
					'title '=> null
					,'value' => '0'
					,'label' => Mage::helper('directory')->__('-- Please select --')
				)
			);
		}
		return $options;
	}

	/**
	 * @override
	 * @return Df_Directory_Model_Resource_Region_Collection
	 */
	protected function _initSelect() {
		parent::_initSelect();
		$locale = Mage::app()->getLocale()->getLocaleCode();
		$this->addBindParam(':region_locale', $locale);
		$this->getSelect()->joinLeft(
			array('rname' => $this->_regionNameTable),'main_table.region_id = rname.region_id AND rname.locale = :region_locale',array('name'));
		return $this;
	}
	/** @var string */
	protected $_countryTable;
	/** @var string */
	protected $_regionNameTable;

	/** @return array(string => Df_Directory_Model_Region) */
	private function getMapFromNameToItem() {
		if (!isset($this->{__METHOD__})) {
			/** @var array(string => Df_Directory_Model_Region) $result  */
			$result = array();
			foreach ($this->getItems() as $region) {
				/** @var Df_Directory_Model_Region $region */
				$result[mb_strtoupper($region->getNameOriginal())] = $region;
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_init(Df_Directory_Model_Region::mf(), Df_Directory_Model_Resource_Region::mf());
		$this->_countryTable = rm_table('directory/country');
		$this->_regionNameTable = rm_table('directory/country_region_name');
	}
	const _CLASS = __CLASS__;

	/** @return Df_Directory_Model_Resource_Region_Collection */
	public static function i() {return new self;}
}