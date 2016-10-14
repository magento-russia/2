<?php
class Df_Directory_Model_Resource_Region_Collection extends Df_Core_Model_Resource_Collection {
	/**
	 * В оригинале имеется метод
	 * @see Mage_Directory_Model_Mysql4_Region_Collection::addCountryCodeFilter(),
	 * однако он нигде не используется ни в Magento CE 1.4.0.1, ни в Magento CE 1.9.1.0
	 * (и, скорей всего, в промежуточных версиях тоже)
	 */
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
	* Filter region by its code or name
	*
	* @param string|array $region
	* @return Mage_Directory_Model_Resource_Region_Collection
	*/
	public function addRegionCodeOrNameFilter($region) {
		if (!empty($region)) {
			$condition = is_array($region) ? array('in' => $region) : $region;
			$this->addFieldToFilter(
				array('main_table.code', 'main_table.default_name'), array($condition, $condition)
			);
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
		return is_null($region) ? null : df_nat0($region->getId());
	}

	/**
	 * @param string $name
	 * @return Df_Directory_Model_Region|null
	 */
	public function getItemByName($name) {
		return dfa($this->getMapFromNameToItem(), mb_strtoupper($name));
	}

	/**
	 * @override
	 * @return Df_Directory_Model_Resource_Region
	 */
	public function getResource() {return Df_Directory_Model_Resource_Region::s();}

	/**
	 * Convert collection items to select options array
	 * @return array
	 */
	public function toOptionArray() {
		$options = $this->_toOptionArray('region_id', 'default_name', array('title' => 'default_name'));
		if ($options) {
			array_unshift($options, array(
				'title '=> null
				,'value' => '0'
				,'label' => Mage::helper('directory')->__('-- Please select --')
			));
		}
		return $options;
	}

	/**
	 * @override
	 * @return Df_Directory_Model_Resource_Region_Collection
	 */
	protected function _initSelect() {
		parent::_initSelect();
		$this->addBindParam(':region_locale', rm_locale());
		$this->getSelect()->joinLeft(
			array('rname' => $this->_regionNameTable)
			,'main_table.region_id = rname.region_id AND rname.locale = :region_locale'
			,array('name'))
		;
		return $this;
	}

	/** @return array(string => Df_Directory_Model_Region) */
	private function getMapFromNameToItem() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				/** @uses Df_Directory_Model_Region::getNameOriginal() */
				array_combine(df_t()->strtoupper($this->walk('getNameOriginal')), $this->getItems())
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		$this->_itemObjectClass = Df_Directory_Model_Region::_C;
		$this->_countryTable = df_table(Df_Directory_Model_Resource_Country::TABLE);
		$this->_regionNameTable = df_table(Df_Directory_Model_Resource_Region::TABLE__NAME);
		$this->addOrder('name', Varien_Data_Collection::SORT_ORDER_ASC);
		$this->addOrder('default_name', Varien_Data_Collection::SORT_ORDER_ASC);
	}
	/** @var string */
	protected $_countryTable;
	/** @var string */
	protected $_regionNameTable;

	const _C = __CLASS__;
}